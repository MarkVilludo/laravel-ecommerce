<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Product;
use App\Models\ActivityLog;
use App\User;

class DashboardController extends Controller
{

   
    public function __construct(Order $order, Promo $promo, Product $product, ActivityLog $activityLog, User $user)
    {
        $this->order = $order;
        $this->promo = $promo;
        $this->product = $product;
        $this->activityLog = $activityLog;
        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        //get available promos
        $promos = $this->promo->getPromos(5);
        //get products with minimal stocks
        $products = $this->product->getProductMinimalStocks(5);
        //get current activity in app
        $activityLogs = $this->activityLog->getRecentActivity(5);

        $data['orders']  =  $this->order->getOrders();
        $data['promos']  = paginateCollection($promos);
        $data['products']  = paginateCollection($products);
        $data['activity_logs']  = paginateCollection($activityLogs);
        //Get active users
        $data['active_customers']  = $this->user->CountActiveCustomers();
        //get orders for today
        $data['total_orders_today']  = $this->order->GetOrdersCount();

        //get pending or processing orders
        $data['total_orders_today']  = $this->order->GetOrdersCount();

        $data['total_proccessing_orders']  = $this->order->CountPendingOrders();


        return $data;
    }
}
