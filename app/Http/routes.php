<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'web'], function ()
{
    Route::group(['middleware' => 'csrf'], function ()
    {
        Route::get('password/reset/{p_Token}', 'RemindersController@getReset');
        Route::post('password/reset', 'RemindersController@postReset');

        Route::get('/', 'BaseController@getRoot');
        Route::post('/login', 'BaseController@postLogin');

        Route::post('client/add', 'ClientsController@addClient');

        Route::get('forgotPassword', 'RemindersController@getForgotPassword');
        Route::post('forgotPassword', 'RemindersController@sendResetLink');

        Route::get('remind', 'RemindersController@getRemind');
        Route::post('remind', 'RemindersController@postRemind');
        Route::get('reset/{token}', 'RemindersController@getReset');
        Route::post('reset', 'RemindersController@postReset');

        Route::group(['middleware' => 'companyOrAdmin'], function ()
        {
            Route::get('logout', 'BaseController@getLogout');

            Route::get('company/edit/{p_CompanyId?}','CompanyController@getEditCompanyLayout');
            Route::post('company/update','CompanyController@editCompany');
            Route::post('company/contacts','CompanyController@manageContacts');
            Route::get('company/address/add/{p_CompanyId?}','CompanyController@addAddress');
            Route::get('company/address/{p_AddressId}','CompanyController@editAddress');
            Route::get('company/address/deactivate/{p_AddressId}','CompanyController@deactivateAddress');
            Route::post('company/address','CompanyController@postAddress');
            Route::post('company/auths','CompanyController@manageAuths');
            Route::get('company/offers/{p_CompanyId?}','OfferController@getOfferListLayout');
            Route::get('company/prizes/{p_CompanyId?}','OfferController@getPrizeListLayout');
            Route::get('offer/add/{p_Type}/{p_CompanyId}','OfferController@add');
            Route::get('offer/edit/{p_OfferId}','OfferController@edit');
            Route::post('offer','OfferController@postOffer');
            Route::post('offer/get','OfferController@get');
            Route::get('offer/deactivate/{p_OfferId}','OfferController@deactivate');
            Route::get('offer/statistics/{p_OfferId}','OfferController@getStatistics');
            Route::get('/dt/offerStatistics','PointsController@getDTOfferStatistics');
            Route::get('dt/clients','ClientsController@getDTClients');
            Route::get('client/search','ClientsController@searchClient');
            Route::get('client/statistics/{p_ClientId}','ClientsController@getStatistics');
            Route::get('dt/pointsLog','PointsController@getDTPointsLog');
            Route::get('company/contact/{p_CompanyId}','CompanyController@getContactCompanyLayout');
            Route::get('company/auth/{p_CompanyId}','CompanyController@getAuthCompanyLayout');
            Route::get('company/{p_CompanyId}/editAuth/{p_AuthId?}', 'CompanyController@getEditAuth');
            Route::get('company/{p_CompanyId}/editContact/{p_ContactId?}', 'CompanyController@getEditContact');
            Route::post('company/editContact', 'CompanyController@editContact');
            Route::get('company/{p_CompanyId}/deleteContact/{p_ContactId}', 'CompanyController@deleteContact');
            Route::post('company/editAuth', 'CompanyController@editAuth');
            Route::get('company/{p_CompanyId}/deactivateAuth/{p_AuthId}', 'CompanyController@deactivateAuth');

            Route::group(['middleware' => 'admin'], function ()
            {
                Route::get('client/edit/{p_ClientId?}','ClientsController@getEditClientLayout');
                Route::get('company/add','CompanyController@getAddCompanyLayout');
                Route::post('company/add', 'CompanyController@editCompany');
                Route::get('company/search','CompanyController@searchCompany');
                Route::get('dt/companies','CompanyController@getDTCompanies');
                Route::get('company/deactivate/{p_CompanyId}','CompanyController@deactivateCompany');
                Route::get('client/deactivate/{p_ClientId}', 'ClientsController@deactivateClient');
                Route::get('company/statistics/{p_CompanyId}','CompanyController@getStatistics');

                Route::post('client/update', 'ClientsController@updateClient');
                Route::get('company/getByTradeName/{p_Pattern?}','CompanyController@getByTradeName');
                Route::get('company/getByCnpj/{p_Cnpj}','CompanyController@getByCnpj');
                Route::get('company/getByContactName/{p_Pattern}','CompanyController@getByContactName');

                Route::get('companyType/search','CompanyTypeController@search');
                Route::get('companyType/edit/{p_Id?}','CompanyTypeController@edit');
                Route::post('companyType/edit','CompanyTypeController@post');
                Route::get('companyType/deactivate/{p_Id}','CompanyTypeController@deactivate');

                Route::get('help','ConfigController@getHelpItems');
                Route::get('help/edit/{p_Id?}','ConfigController@editHelpItem');
                Route::post('help/edit','ConfigController@postHelpItem');
                Route::get('help/delete/{p_Id}','ConfigController@deleteHelpItem');

                Route::get('help','ConfigController@getHelpItems');
                Route::get('help/edit/{p_Id?}','ConfigController@editHelpItem');
                Route::post('help/edit','ConfigController@postHelpItem');
                Route::get('help/delete/{p_Id}','ConfigController@deleteHelpItem');

                Route::get('usage_terms','ConfigController@getUsageTerms');
                Route::post('usage_terms','ConfigController@postUsageTerms');

                Route::get('about','ConfigController@getAbout');
                Route::post('about','ConfigController@postAbout');
            });
        });
    });


    Route::get('m/getReset/{p_Email}', 'RemindersController@getMobileReset');
    Route::post('/m', 'BaseController@postMobileLogin');
    Route::post('/m/facebookLogin', 'ClientsController@postFacebookLogin');
    Route::post('m/register', 'ClientsController@mobileRegister');
    Route::get('m/help', 'ConfigController@getMobileHelp');
    Route::get('m/about', 'ConfigController@getMobileAbout');
    Route::get('m/usage_terms', 'ConfigController@getMobileUsageTerms');

    Route::group(['middleware' => 'client'], function ()
    {
        Route::get('m/companyTypes', 'CompanyTypeController@getCompanyTypes');
        Route::post('m/client/update', 'ClientsController@updateClient');
        Route::get('m/logout', 'BaseController@getMobileLogout');

        Route::get('m/getBonusPrizes', 'OfferController@getBonusPrizes');
        Route::get('m/companies/withPoints', 'CompanyController@getWithPoints');
        Route::get('m/offers', 'OfferController@getOffers');
        Route::get('m/companies', 'CompanyController@getCompanies');
        Route::get('m/offer', 'OfferController@claimOffer');

        Route::get('m/getPointsLog', 'PointsController@getMobilePointsLog');
    });
});