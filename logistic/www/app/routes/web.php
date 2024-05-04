<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@home')->name('home');
Route::get('/charts/orders-bang-tuong', 'HomeController@chartOrdersBangTuong')->name('chart.orders-bang-tuong');
Route::get('/charts/customers', 'App\\ChartController@chartCustomers')->name('chart.customers');
Route::get('/charts/revenue', 'HomeController@chartRevenue')->name('chart.revenue');

Route::get('/trucks', 'App\\TruckController@index')->name('truck.index');
Route::get('/completed-trucks', 'App\\TruckController@getCompleted')->name('truck.completed');
Route::post('/trucks', 'App\\TruckController@create')->name('truck.create');
Route::post('/trucks/orders/delete/{order}', 'App\\TruckController@deleteOrder')->name('truck.orders.delete');
Route::get('/trucks/{truck}', 'App\\TruckController@show')->name('truck.show');
Route::post('/trucks/{truck}', 'App\\TruckController@update')->name('truck.update');
Route::post('/trucks/{truck}/delete', 'App\\TruckController@destroy')->name('truck.delete');
Route::post('/trucks/{truck}/orders', 'App\\TruckController@addOrder')->name('truck.add_order');
Route::post('/trucks/{truck}/bulk_orders', 'App\\TruckController@addOrders')->name('truck.add_bulk_orders');
Route::post('/trucks/{truck}/update_location', 'App\\TruckController@updateLocation')->name('truck.update_location');

Route::get('/locations', 'App\\LocationController@index')->name('location.index');
Route::post('/locations', 'App\\LocationController@create')->name('location.create');
Route::post('/locations/{location}', 'App\\LocationController@update')->name('location.update');
Route::post('/locations/{location}/delete', 'App\\LocationController@destroy')->name('location.delete');

Route::post('/shipping_methods', 'App\\LocationController@createShippingMethod')->name('shipping_method.create');
Route::post('/shipping_methods/{shippingMethod}', 'App\\LocationController@updateShippingMethod')->name('shipping_method.update');
Route::post('/shipping_methods/{shippingMethod}/delete', 'App\\LocationController@destroyShippingMethod')->name('shipping_method.delete');

Route::get('/customers', 'App\\CustomerController@index')->name('customer.index');
Route::post('/customers', 'App\\CustomerController@create')->name('customer.create');
Route::get('/customers/{customer}', 'App\\CustomerController@show')->name('customer.show');
Route::post('/customers/{customer}', 'App\\CustomerController@update')->name('customer.update');
Route::get('/customers/{customer}/orders', 'App\\CustomerController@showOrders')->name('customer.orders');
Route::get('/customers/{customer}/orders/completed', 'App\\CustomerController@showCompletedOrders')->name('customer.orders.completed');
Route::post('/customers/{customer}/orders/notify_address', 'App\\CustomerController@notifyAddress')->name('customer.orders.notify_address');
Route::post('/customers/{customer}/update_user', 'App\\CustomerController@updateUser')->name('customer.update_user');
Route::post('/customers/{customer}/delete', 'App\\CustomerController@destroy')->name('customer.delete');

Route::get('/reports', 'App\\ReportController@index')->name('report.index');
Route::post('/reports', 'App\\ReportController@update')->name('report.update');

Route::get('/top_revenue', 'App\\TopRevenueController@index')->name('top_revenue.index');
Route::get('/top_revenue/customers/{hunterId}', 'App\\TopRevenueController@getCustomers')->name('top_revenue.customers');

Route::get('/sellers', 'App\\SellerController@index')->name('seller.index');
Route::get('/sellers/{id}', 'App\\SellerController@find')->name('seller.find');
Route::get('/sellers/{id}/total_commission', 'App\\SellerController@totalCommission')->name('seller.total_commission');

Route::get('/users', 'App\\UserController@index')->name('user.index');
Route::post('/users', 'App\\UserController@create')->name('user.create');
Route::get('/users/{user}', 'App\\UserController@show')->name('user.show');
Route::post('/users/{user}', 'App\\UserController@update')->name('user.update');
Route::post('/users/{user}/delete', 'App\\UserController@destroy')->name('user.delete');
Route::post('/users/profile/change_password', 'App\\UserController@changePassword')->name('user.change_password');

Route::get('/orders', 'App\\OrderController@index')->name('order.index');
Route::get('/orders/locations/{location}', 'App\\OrderController@getOrdersFromLocation')->name('order.location');
Route::get('/orders/vietnamese_inventory', 'App\\OrderController@vietnameseInventory')->name('order.vietnamese_inventory');
Route::get('/orders/vietnamese_inventory/{location}', 'App\\OrderController@vietnameseInventoryFromLocation')->name('order.vietnamese_inventory.location');
Route::get('/orders/noname', 'App\\OrderController@getOrdersOfNoName')->name('order.noname');
Route::get('/orders/express', 'App\\OrderController@getOrdersOfExpress')->name('order.express');
Route::get('/orders/unpaid', 'App\\OrderController@getUnpaidOrders')->name('order.unpaid');

Route::post('/orders/calculate_costs', 'App\\OrderController@calculateCosts')->name('order.calculate_costs');

Route::post('/orders/process_express', 'App\\OrderController@processExpressOrders')->name('order.process_express');
Route::post('/orders/{order}/merge', 'App\\OrderController@merge')->name('order.merge');
Route::post('/orders', 'App\\OrderController@create')->name('order.create');

Route::get('/orders/{order}', 'App\\OrderController@show')->name('order.show');
Route::post('/orders/{order}', 'App\\OrderController@update')->name('order.update');
Route::post('/orders/{order}/calculate_cost', 'App\\OrderController@calculateCost')->name('order.calculate_cost');
Route::post('/orders/{order}/update_status', 'App\\OrderController@updateStatus')->name('order.update_status');
Route::post('/orders/{order}/update_note_in_list', 'App\\OrderController@updateNoteInList')->name('order.update_note_in_list');
Route::post('/orders/{order}/update_note_in_truck', 'App\\OrderController@updateNoteInTruck')->name('order.update_note_in_truck');
Route::post('/orders/{order}/update_note_in_vn_inventory', 'App\\OrderController@updateNoteInVnInventory')->name('order.update_note_in_vn_inventory');
Route::post('/orders/{order}/delete', 'App\\OrderController@destroy')->name('order.delete');
Route::post('/orders/merge/check_order_codes', 'App\\OrderController@checkOrderCodes')->name('order.bulk_merge.check_order_ids');
Route::post('/orders/merge/bulk', 'App\\OrderController@bulkMerge')->name('order.bulk_merge');
Route::get('/address', 'App\\OrderController@address')->name('order.address');

Route::get('/messages/{order}', 'App\\MessageController@get')->name('message.get');
Route::post('/messages', 'App\\MessageController@create')->name('message.create');

Route::post('/order_declaration', 'App\\OrderDeclarationController@create')->name('order_declaration.create');
Route::post('/order_declaration/{declaration}/delete', 'App\\OrderDeclarationController@destroy')->name('order_declaration.delete');

Route::post('/packs', 'App\\PackController@create')->name('pack.create');
Route::post('/packs/{pack}', 'App\\PackController@update')->name('pack.update');
Route::post('/packs/{pack}/update_status', 'App\\PackController@updateStatus')->name('pack.update_status');
Route::post('/packs/{pack}/delete', 'App\\PackController@destroy')->name('pack.delete');

Route::post('/payments', 'App\\PaymentController@create')->name('payment.create');
Route::post('/payments{payment}/delete', 'App\\PaymentController@destroy')->name('payment.delete');

Route::get('/transactions', 'App\\TransactionController@index')->name('transaction.index');
Route::get('/transactions/autobank', 'App\\TransactionController@autobank')->name('transaction.autobank');
Route::get('/transactions/create', 'App\\TransactionController@showCreateForm')->name('transaction.create');
Route::post('/transactions/create', 'App\\TransactionController@create')->name('transaction.create');
Route::get('/transactions/{transaction}', 'App\\TransactionController@show')->name('transaction.show');
Route::get('/transactions/customers/create', 'App\\TransactionController@showCreateByCustomerForm')->name('transaction.customer.create');
Route::post('/transactions/customers/create', 'App\\TransactionController@createByCustomer')->name('transaction.customer.create');
Route::post('/transactions/{transaction}/accept', 'App\\TransactionController@accept')->name('transaction.accept');
Route::post('/transactions/{transaction}/cancel', 'App\\TransactionController@cancel')->name('transaction.cancel');

Route::get('/payables/orders', 'App\\PayableController@getOrders')->name('payable.orders');
Route::post('/payables/pay', 'App\\PayableController@pay')->name('payable.pay');
Route::post('/payables/debt', 'App\\PayableController@getDebt')->name('payable.debt');

Route::get('/cost_china', 'App\\CostChinaController@index')->name('cost_china.index');
Route::post('/cost_china', 'App\\CostChinaController@create')->name('cost_china.create');

Route::post('/options', 'App\\OptionController@update')->name('option.update');
Route::post('/options/noti-debt', 'App\\OptionController@notiDebt')->name('option.noti-debt');

Route::get('/logs', 'App\\LogController@index')->name('log.index');

Route::any('/telegram_bots/webhook', 'App\\TelegramBotController@webhook')->name('telegram_bots.webhook');
