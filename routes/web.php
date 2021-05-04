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
    Route::get('/studies', 'SitesController@studies')->middleware('perm:6');
    Route::post('/studies', 'SitesController@create_study')->middleware('perm:6');
    Route::get('ajax/studies', 'SitesController@studiesDT')->name('studies-dt')->middleware('perm:6');
    Route::get('studies/{_id}', 'SitesController@edit_study')->name('edit-study')->middleware('perm:6');
    Route::put('studies', 'SitesController@update_study')->name('update-study')->middleware('perm:6');
    Route::delete('studies/{_id}', 'SitesController@delete_study')->name('delete-study')->middleware('perm:6');


    //site studies
    Route::get('/site_studies', 'SitesController@site_studies')->middleware('perm:8');
    Route::post('/site_studies', 'SitesController@create_site_study')->middleware('perm:8');
    Route::get('ajax/site_studies', 'SitesController@site_studiesDT')->name('site-studies-dt')->middleware('perm:8');
    Route::delete('site_studies/{_id}', 'SitesController@delete_site_study')->name('delete-site-study')->middleware('perm:8');

    //sites
    Route::get('/sites', 'SitesController@sites')->middleware('perm:7');
    Route::post('/sites', 'SitesController@create_site')->middleware('perm:7');
    Route::get('ajax/sites', 'SitesController@sitesDT')->name('sites-dt')->middleware('perm:7');
    Route::get('sites/{_id}', 'SitesController@edit_site')->name('edit-site')->middleware('perm:7');
    Route::put('sites', 'SitesController@update_site')->name('update-site')->middleware('perm:7');
    Route::delete('sites/{_id}', 'SitesController@delete_site')->name('delete-site')->middleware('perm:7');

    //strata
    Route::get('/strata', 'AllocationController@strata')->middleware('perm:2');
    Route::post('/strata', 'AllocationController@create_strata')->middleware('perm:2');
    Route::get('ajax/strata', 'AllocationController@strataDT')->name('strata-dt')->middleware('perm:2');
    Route::get('strata/{_id}', 'AllocationController@edit_strata')->name('edit-strata')->middleware('perm:2');
    Route::put('strata', 'AllocationController@update_strata')->name('update-strata')->middleware('perm:2');
    Route::delete('strata/{_id}', 'AllocationController@delete_strata')->name('delete-strata')->middleware('perm:2');

    //allocation list
    Route::get('/allocation/upload', 'AllocationController@upload_list')->middleware('perm:3');
    Route::post('/allocation/upload', 'AllocationController@upload')->middleware('perm:3');



    //randomization
    Route::get('/randomization', 'RandomizationController@randomization')->middleware('perm:4');
    Route::post('/randomization', 'RandomizationController@randomizationFiltered')->middleware('perm:4');
    Route::get('ajax/randomization', 'RandomizationController@randomizationDT')->name('randomization-dt')->middleware('perm:4');


    //sms
    Route::get('/sms', 'RandomizationController@sms')->middleware('perm:5');
    Route::post('/sms', 'RandomizationController@smsFiltered')->middleware('perm:5');
    Route::get('ajax/sms', 'RandomizationController@smsDT')->name('sms-dt')->middleware('perm:5');

    //mails
    Route::get('/mails/bulk', 'MailController@bulk_mails')->middleware('perm:9');
    Route::post('/mails/bulk', 'MailController@create_bulk_mail')->middleware('perm:9');
    Route::get('ajax/mails/bulk', 'MailController@bulkMailsDT')->name('bulk-mails-dt')->middleware('perm:9');

    Route::group(['middleware' => ['perm:1']], function () {

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
        Route::put('user_groups', 'UserController@update_group_details');//->middleware('perm:1');
        Route::post('user_groups/delete', 'UserController@delete_group');//->middleware('perm:1');

        Route::get('ajax/users/groups/details/{id}', 'UserController@userGroupDetailsDT');//->middleware('perm:1');
        Route::get('users/groups/{id}','UserController@user_group_details');//->middleware('perm:1');
        Route::post('/users/groups/permissions/add','UserController@add_group_permission');//->middleware('perm:1');
        Route::get('/users/groups/permissions/delete/{id}','UserController@delete_group_permission');//->middleware('perm:1');


    });

    /*
    *
    * end of Admin stuff
    */

});
