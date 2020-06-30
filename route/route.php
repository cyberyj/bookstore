<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('show_index','index/index/show_index');//显示书店主页
Route::get('show_login','user/user/show_login');//显示用户登录
Route::get('show_register','user/user/show_register');//显示用户注册
Route::get('show_details','product/product/show_details');//显示书本详情
Route::get('show_admin_login','admin/admin/show_admin_login');//显示管理员登录
Route::get('show_admin_welcome','admin/admin/show_admin_welcome');//显示管理员欢迎界面
Route::get('show_member_list','admin/admin/show_member_list');//显示会员列表
Route::get('show_member_edit/:uid','admin/admin/show_member_edit');//显示编辑用户
Route::get('show_admin_index','admin/admin/show_admin_index');//显示管理员主页
Route::get('show_plist','product/product/show_plist');//显示商品列表
Route::get('show_check','product/product/show_check');//显示商品结算
Route::get('show_cart','product/product/show_cart');//显示购物车
Route::get('admin_logout','admin/admin/admin_logout');//管理员退出
Route::post('admin_login','admin/admin/admin_login');//管理员登录
Route::post('member_delete','admin/admin/member_delete');//会员删除
Route::post('member_edit','admin/admin/member_edit');
Route::post('user_check','user/user/user_check');//用户姓名是否重复
Route::post('user_register','user/user/user_register');//用户注册
Route::post('user_login','user/user/user_login');//用户登录
Route::get('show_book_list','admin/admin/show_book_list');
Route::get('show_book_add','admin/admin/show_book_add');
Route::get('show_member_add','admin/admin/show_member_add');
Route::get('show_index','index/index/show_index');
Route::get('show_login','user/user/show_login');
Route::get('show_register','user/user/show_register');
Route::get('show_details','product/product/show_details');
Route::get('show_admin_login','admin/admin/show_admin_login');
Route::get('show_admin_welcome','admin/admin/show_admin_welcome');
Route::get('show_member_list','admin/admin/show_member_list');
Route::get('show_member_edit/:uid','admin/admin/show_member_edit');
Route::get('show_book_edit/:bid','admin/admin/show_book_edit');
Route::get('admin_logout','admin/admin/admin_logout');
Route::post('book_delete','admin/admin/book_delete');
Route::post('book_edit','admin/admin/book_edit');
Route::get('show_admin_index','admin/admin/show_admin_index');
Route::get('show_order_list','admin/admin/show_order_list');
Route::get('show_order_edit','admin/admin/show_order_edit');
Route::post('order_delete','admin/admin/order_delete');
Route::post('order_delete_s','admin/admin/order_delete_s');
Route::post('member_delete_s','admin/admin/member_delete_s');
Route::post('book_delete_s','admin/admin/book_delete_s');
Route::post('order_update','admin/admin/order_update');
Route::get('show_plist','product/product/show_plist');
Route::get('show_cart','product/product/show_cart');
Route::get('catalog_info','product/product/catalog_info');
Route::post('admin_login','admin/admin/admin_login');
Route::post('user_delete','admin/admin/user_delete');
Route::post('user_check','user/user/user_check');
Route::post('user_register','user/user/user_register');
Route::post('user_login','user/user/user_login');
Route::post("book_add",'admin/admin/book_add');
Route::post("book_detail",'product/product/book_detail');
Route::post("add_cart",'product/product/add_cart');
Route::post("book_header",'product/product/book_header');
Route::post("remove_cart_item",'product/product/remove_cart_item');
Route::post("cart_item_num",'product/product/cart_item_num');
Route::get("show_user_order",'user/user/show_user_order');
Route::get("user_logout",'user/user/user_logout');
Route::post("check_out",'product/product/check_out');
Route::post("get_cart_info",'product/product/get_cart_info');
Route::post("delete_cart_item",'product/product/delete_cart_item');
return [

];
