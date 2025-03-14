<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\customer\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_items' => ['required', 'array'],
            'order_items.*' => ['required', 'array'],
            'order_items.*.product_id' => ['required', 'exists:products,id'],
            'order_items.*.quantity' => ['required', 'integer', 'min:1'],
            'shipping_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::find(Auth::user()->id);
        $orderItems = $request->order_items;
        $totalPrice = 0;
        foreach ($orderItems as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json(['message' => 'Product not available or out of stock'], 400);
            }
            $price = $product->price;
            if ($product->promotions->count() > 0) {
                $promotion = $product->promotions()->orderBy('created_at', 'desc')->first();
                $date = Carbon::now();
                if ($date >= $promotion->start_date && $date <= $promotion->end_date) {
                    $price = $product->price * ((100 - $promotion->discount_percentage) / 100);
                }
            }
            $totalPrice += $price * $item['quantity'];
        }
        $order = $user->orders()->create([
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'phone_number' => $request->phone_number,
        ]);
        foreach ($orderItems as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->price;
            if ($product->promotions->count() > 0) {
                $promotion = $product->promotions()->orderBy('created_at', 'desc')->first();
                $date = Carbon::now();
                if ($date >= $promotion->start_date && $date <= $promotion->end_date) {
                    $price = $product->price * ((100 - $promotion->discount_percentage) / 100);
                }
            }
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
            $product = Product::find($item['product_id']);
            $product->stock = $product->stock - $item['quantity'];
            $product->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'order created successfully'
        ], 201);
    }
    public function index(){
        $orders = Order::with('orderItems')->where('user_id','=',Auth::user()->id)->get();
        if($orders->isEmpty()){
            response()->json([
                'success' => false,
                'message' => 'there are not any order'
            ],404);
        }
        return response()->json([
            'success' => true,
            'orders' => OrderResource::collection($orders)
        ],200);
    }
    public function show($id){
        $order = Order::with('orderItems')->find($id);
        if(!$order){
            return response()->json([
                'success' => 'false',
                'message' => 'order is not found'
            ],404);
        }
        if($order->user_id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ],403);
        }
        return response()->json([
            'success' => true,
            'order' => new OrderResource($order)
        ],200);
    }

    public function pay(Request $request, $id)    
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $order = Order::find($id);
        if(!$order){
            return response()->json([
                'success' => false,
                'message' => 'order is not found'
            ],404);
        }
        if($order->status == 'completed'){
            return response()->json([
                'success' => false,
                'message' => 'order already paid'
            ],400);
        }
        
        $lineItems = [];

        foreach ($order->orderItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => $item->price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => url('api/customer/payment-success?order_id=' . $order->id),
        'cancel_url' => url('api/customer/payment-cancel?order_id=' . $order->id),
        ]);

        return response()->json([
            'success' => true,
            'paymentUrl' => $session->url
        ],200);
    }
    public function paymentSuccess(Request $request)
    {
        $order = Order::findOrFail($request->query('order_id'));
        $order->update(['payment_status' => 'paid', 'status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful'
        ],200);
    }

    public function paymentCancel(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->update(['payment_status' => 'failed', 'status' => 'canceled']);

        return response()->json([
            'success' => false,
            'message' => 'Payment canceled'
        ],400);
    }

    
    
}
