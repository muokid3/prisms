<?php

use Illuminate\Support\Facades\Route;

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
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/','HomeController@index');



    Route::get('edit-profile', 'UserController@editProfile');
    Route::get('my-profile', 'UserController@myProfile');
    Route::put('edit-profile', 'UserController@updateProfile');
    Route::put('change-password', 'UserController@updatePassword');



    /*
     *
     * Admin stuff
     */

    //studies
    Route::get('/studies', 'SitesController@studies');
    Route::post('/studies', 'SitesController@create_study');
    Route::get('ajax/studies', 'SitesController@studiesDT')->name('studies-dt');
    Route::delete('studies/{_id}', 'SitesController@delete_study')->name('delete-study');


    //site studies
    Route::get('/site_studies', 'SitesController@site_studies');
    Route::post('/site_studies', 'SitesController@create_site_study');
    Route::get('ajax/site_studies', 'SitesController@site_studiesDT')->name('site-studies-dt');
    Route::delete('site_studies/{_id}', 'SitesController@delete_site_study')->name('delete-site-study');

    //sites
    Route::get('/sites', 'SitesController@sites');
    Route::post('/sites', 'SitesController@create_site');
    Route::get('ajax/sites', 'SitesController@sitesDT')->name('sites-dt');
    Route::delete('sites/{_id}', 'SitesController@delete_site')->name('delete-site');


    //studies
    Route::get('/randomization', 'RandomizationController@randomization');
    Route::post('/randomization', 'RandomizationController@create_randomization');
    Route::get('ajax/randomization', 'RandomizationController@randomizationDT')->name('randomization-dt');
    Route::delete('randomization/{_id}', 'RandomizationController@delete_randomization')->name('delete-randomization');


    //sms
    Route::get('/sms', 'RandomizationController@sms');
    Route::get('ajax/sms', 'RandomizationController@smsDT')->name('sms-dt');




//USERS
    Route::get('/users', 'UserController@users');
    Route::post('/enroll', 'UserController@register_user');//->middleware('perm:1');
    Route::get('ajax/users', 'UserController@usersDT')->name('users-dt');

    Route::get('users/{_id}', 'UserController@edit_user')->name('edit-user');
    Route::put('enroll', 'UserController@update_user')->name('update-user');
    Route::delete('users/{_id}', 'UserController@delete_user')->name('delete-user');

    Route::get('/user_groups', 'UserController@user_groups');//->middleware('perm:1');
    Route::post('/user_groups', 'UserController@new_user_group');//->middleware('perm:1');
    Route::get('user_groups/{_id}', 'UserController@get_group_details');//->middleware('perm:1');
    Route::post('user_groups/update', 'UserController@update_group_details');//->middleware('perm:1');
    Route::post('user_groups/delete', 'UserController@delete_group');//->middleware('perm:1');

    Route::get('ajax/users/groups/details/{id}', 'UserController@userGroupDetailsDT');//->middleware('perm:1');
    Route::get('users/groups/{id}','UserController@user_group_details');//->middleware('perm:1');
    Route::post('/users/groups/permissions/add','UserController@add_group_permission');//->middleware('perm:1');
    Route::get('/users/groups/permissions/delete/{id}','UserController@delete_group_permission');//->middleware('perm:1');

//end of some shit i dont understand


    /*
    *
    * end of Admin stuff
    */

});
