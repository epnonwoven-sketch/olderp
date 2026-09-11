<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BagController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\ImportOldRecords;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OtherRegister;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('User.login');
});
Route::get("/test", function () {
    return view("Layout/test");
});
Route::middleware(['auth:sanctum', 'activity'])->group(function () {
    Route::match(["get", "post"], "/loginCheck", function () {
        $lastActivity = session('last_activity');
        $timeout = 60; // 10  minutes
        if (Auth::check()) {
            if ($lastActivity && (Carbon::now()->diffInMinutes($lastActivity)) > $timeout && env('LAST_ACTIVITY_NOT_LOG', false) != true) {

                session()->forget('last_activity');
                if (Request()->ajax()) {
                    return responseMsg(false, "ideal", "");
                }
                Auth::logout();
                return redirect('/login')->with('message', 'You have been logged out due to inactivity.');
            }
            if (Request()->ajax()) {
                return responseMsg(true, "active", [$lastActivity, (Carbon::now()->diffInMinutes($lastActivity)) > $timeout, (Carbon::now()->diffInMinutes($lastActivity))]);
            }
        } else {
            if (Request()->ajax()) {
                return responseMsg(true, "active", [$lastActivity, (Carbon::now()->diffInMinutes($lastActivity)) > $timeout, $timeout]);
            }
            return redirect('/login')->with('message', 'You have been logged out due to inactivity.');
        }
    })->name("activity.test")->withoutMiddleware("activity");
    Route::controller(Dashboard::class)->group(function () {
        Route::get('/home', "home")->name("home");
        Route::get("dashboard/loop-status", "loopStatus")->name("dashboard.loop.status");
        Route::get("dashboard/roll-status", "rollStatus")->name("dashboard.roll.status");
        Route::get("dashboard/daily/production", "dailyProduction")->name("dashboard.daily.production");
    });
    Route::controller(MasterController::class)->group(function () {
        Route::get("master/fare/list", "fareList")->name("master.fare.list");
        Route::post("master/fare/add", "addFare")->name("master.fare.add");
        Route::get("master/fare/dtl/{id?}", "fareDtl")->name("master.fare.dtl");
        Route::post("master/fare/deactivate/{id}", "deactivateFare")->name("master.fare.deactivate");

        Route::get("master/stereo/list", "stereoList")->name("master.stereo.list");
        Route::post("master/stereo/add", "addStereo")->name("master.stereo.add");
        Route::get("master/stereo/dtl/{id?}", "stereoDtl")->name("master.stereo.dtl");
        Route::post("master/stereo/deactivate/{id}", "deactivateStereo")->name("master.stereo.deactivate");

        Route::get("master/grade/list", "gradeList")->name("master.grade.list");
        Route::post("master/grade/add", "addGrade")->name("master.grade.add");
        Route::get("master/grade/dtl/{id?}", "gradeDtl")->name("master.grade.dtl");
        Route::post("master/grade/deactivate/{id}", "deactivateGrade")->name("master.grade.deactivate");

        Route::get("master/roll-quality/list", "rollQualityList")->name("master.roll.quality.list");
        Route::post("master/roll-quality/add", "addRollQuality")->name("master.roll.quality.add");
        Route::get("master/roll-quality/dtl/{id?}", "rollQualityDtl")->name("master.roll.quality.dtl");

        Route::get("master/grade-map/list", "gradeListMap")->name("master.grade.map.list");
        Route::get("master/quality-vender-map/list/{venderId}", "rollVenderListMap")->name("master.quality.vender.map.list");

        Route::get("master/rate-type/list", "rateTypeList")->name("master.rate.type.list");
        Route::post("master/rate-type/add", "addRateType")->name("master.rate.type.add");
        Route::get("master/rate-type/dtl/{id?}", "rateTypeDtl")->name("master.rate.type.dtl");
        Route::post("master/rate-type/deactivate/{id}", "deactivateRateType")->name("master.rate.type.deactivate");

        Route::get("master/user-type/list", "userTypeList")->name("master.user.type.list");
        Route::post("master/user-type/add", "addUserType")->name("master.user.type.add");
        Route::get("master/user-type/dtl/{id?}", "userTypeDtl")->name("master.user.type.dtl");

        Route::get("master/loop-stock/list", "loopStockList")->name("master.loop.stock.list");
        Route::post("master/loop-stock/add-edit", "loopStockAddEdit")->name("master.loop.stock.add.edit");
        Route::get("master/loop-stock/dtl/{id?}", "loopStockDtl")->name("master.loop.stock.dtl");
        Route::post("master/loop-stock/deactivateLoopStock/{id}", "deactivateLoopStock")->name("master.loop-stock.deactivate");
        Route::post("master/loop-stock-booking/test", "loopStockTestBooking")->name("master.loop.stock.booking.test");

        Route::get("master/broker/list", "brokerList")->name("master.broker.list");
        Route::post("master/broker/add-edit", "brokerAddEdit")->name("master.broker.add.edit");
        Route::get("master/broker/dtl/{id?}", "brokerDtl")->name("master.broker.dtl");
        Route::post("master/broker/deactivateLoopStock/{id}", "activeDeactivateBroker")->name("master.broker.deactivate");

        Route::get("master/state/list", "stateList")->name("master.state.list");
        Route::post("master/state/add-edit", "stateAddEdit")->name("master.state.add.edit");
        Route::get("master/state/dtl/{id?}", "stateDtl")->name("master.state.dtl");
        Route::post("master/state/deactivateLoopStock/{id}", "activeDeactivateState")->name("master.state.deactivate");
        Route::match(["get", "post"], "master/state-city/import", "importStateAndCity");


        Route::get("master/city/list", "cityList")->name("master.city.list");
        Route::post("master/city/add-edit", "cityAddEdit")->name("master.city.add.edit");
        Route::get("master/city/dtl/{id?}", "cityDtl")->name("master.city.dtl");
        Route::post("master/city/deactivateLoopStock/{id}", "activeDeactivateCity")->name("master.city.deactivate");
        Route::get("master/city-by-state/{id}", "getCityListByState")->name("master.city.by.state");

        /**
         * roll shortage
         */

        Route::get("master/roll/shortage/list", "rollShortageLimitList")->name("master.roll.shortage.list");
        Route::post("master/roll/shortage/add-edit", "rollShortageLimitAddEdit")->name("master.roll.shortage.add.edit");
        Route::post("master/roll/shortage/deactivateLoopStock/{id}", "rollShortageLimitActiveDeactivate")->name("master.roll.shortage.deactivate");
    });

    Route::controller(MenuController::class)->group(function () {
        Route::get("/menu-list", "getMenuList")->name("menu-list");
        Route::get("/menu/update/user-list", "updateMenuUserTypeList")->name("menu.update.user.list");
        Route::post("/menu/update/user-type", "updateMenuByUserType")->name("menu.update.user.type");
        Route::get("/menu-submenu", "getSubMenuList")->name("submenu-list");
        Route::post("/menu-add", "add")->name("menu-add");
        Route::get("/menu-edit/{id}", "getMenuDtl")->name("menu-edit");
        Route::post("/menu-deactivate/{id}", "deactivate")->name("menu-deactivate");
    });
    Route::controller(UserController::class)->group(function () {
        Route::match(["get", "post"], "user/login", "login")->name("login")->withoutMiddleware("auth:sanctum");
        Route::get("user/logout", "logout")->name("logout");
        Route::match(["get", "post"], "user/create", "createUser")->name("createUser");
        Route::get("user/haspassword/{id?}", "hasPassword")->name("haspassword");
        Route::get("user/profile", "profile")->name("profile");
        Route::match(["get", "post"], "user/change-password", "changePassword")->name("change-password");
        Route::get("user/list", "userList")->name("user.list");
        Route::get("user/{id}", "userDtl")->name("user.id");
        Route::post("user/add", "createUser")->name("user.add");
        Route::post("user/edit", "editUser")->name("user.edit");
    });

    Route::controller(VendorController::class)->group(function () {
        Route::get("vender/list", "vendorList")->name("vendor.list");
        Route::post("vender/add", "addVendor")->name("vendor.add");
        Route::get("vender/edit/{id}", "getVenderDtl")->name("vendor.edit");
        Route::post("vender/deactivate/{id}", "deactivate")->name("vendor.deactivate");
    });

    Route::controller(ClientController::class)->group(function () {
        Route::get("client/list", "clientList")->name("client.list");
        Route::post("client/add", "addClient")->name("client.add");
        Route::get("client/edit/{id}", "getClientDtl")->name("client.edit");
        Route::get("client/delete/{id}", "deactivate")->name("client.delete");
    });

    Route::controller(SectorController::class)->group(function () {
        Route::get("sector/list", "sectorList")->name("sector.list");
        Route::post("sector/add-edit", "addEditSector")->name("sector.add.edit");
        Route::get("sector/dtl/{id}", "getSectorDtl")->name("sector.dtl");
        Route::post("sector/deactivate/{id}", "deactivate")->name("sector.deactivate");
    });

    Route::controller(BagController::class)->group(function () {
        Route::get("bag/list", "bagList")->name("bag.list");
        Route::post("bag/add", "addBag")->name("bag.add");
        Route::get("bag/edit/{id}", "getBagDtl")->name("bag.edit");
    });

    Route::controller(ColorController::class)->group(function () {
        Route::get("roll/color/list", "rollColorList")->name("roll.color.list");
        Route::post("roll/color/add", "addRollColor")->name("roll.color.add");
        Route::get("roll/color/edit/{id}", "rollColorDtl")->name("roll.color.edit");
        Route::get("roll/color/delete/{id}", "deactivateRollColor")->name("client.color.delete");

        Route::get("color/list", "colorList")->name("color.list");
        Route::post("color/add", "addColor")->name("color.add");
        Route::get("color/edit/{id}", "colorDtl")->name("color.edit");
        Route::get("color/delete/{id}", "deactivateColor")->name("color.delete");
    });

    Route::controller(TransporterController::class)->group(function () {
        Route::get("transporter/auto/list", "autoList")->name("transporter.auto.list");
        Route::post("transporter/auto/add", "addAuto")->name("transporter.auto.add");
        Route::get("transporter/auto/dtl/{id}", "autoDtl")->name("transporter.auto.dtl");
        Route::post("transporter/auto/deactivate/{id}", "deactivateAuto")->name("transporter.auto.deactivate");

        Route::get("transporter/transporter/list", "transporterList")->name("transporter.transporter.list");
        Route::post("transporter/transporter/add", "addTransporter")->name("transporter.transporter.add");
        Route::get("transporter/transporter/dtl/{id}", "transporterDtl")->name("transporter.transporter.dtl");
        Route::post("transporter/transporter/deactivate/{id}", "deactivateTransporter")->name("transporter.transporter.deactivate");
    });

    Route::controller(RollController::class)->group(function () {
        Route::get("roll/order/punches", "orderPunches")->name("order.punches");
        Route::match(['get', 'post'], "roll/orders/alternateOptionsColor", "alternateOptionsColor")->name("roll.order.alternate.options.color");
        Route::match(['get', 'post'], "roll/orders/alternateOptionsGsm", "alternateOptionsGsm")->name("roll.order.alternate.options.gsm");
        Route::post("roll/order/punches/save", "orderPunchesSave")->name("order.punches.save");
        Route::match(["get", "post"], "roll/order/edit/{orderId}", "orderPunchesEdit")->name("order.punches.edit");
        Route::post("roll/client/old/order", "oldOrderOfClient")->name("client.old.order");
        Route::post("roll/client/order/suggestion", "orderSuggestionClient")->name("client.order.suggestion");
        Route::post("roll/order/re-book-suggestion", "rebookOrder")->name("order.rebook");
        Route::post("roll/order/disabused", "disbursedOrder")->name("order.disabused");
        Route::post("roll/order/deactivate", "deactivateOrder")->name("order.deactivate");
        Route::get("roll/order/disabused/register", "disburseRegister")->name("order.disabused.register");
        Route::post("roll/order/test", "rollTestByOrder")->name("roll.order.test");
        Route::post("roll/delete/transit", "deleteTransit")->name("roll.delete.transit");
        Route::post("roll/order/to/book", "bookRollToOrder")->name("roll.order.to.book");
        Route::post("roll/order/remove/booking", "removeBookedRoll")->name("roll.order.remove.booking");
        Route::post("roll/stock/to/transit", "backInTransit")->name("roll.stock.to.transit");

        Route::match(["get", "post"], "roll/order/book", "bookedOrder")->name("order.book");
        Route::match(["get", "post"], "roll/order/book-delivered", "bookedOrderDelivered")->name("order.book.delivered");
        Route::match(["get", "post"], "roll/order/unbook", "unBookedOrder")->name("order.unbook");
        Route::get("roll/order/draft", "draftOrder")->name("order.draft");

        Route::get("roll/order/status","orderStatus")->name("order.status");

        Route::get("roll/transit/list", "transitList")->name("roll.transit");
        Route::get("roll/transit/dtl/{vendor_id?}", "transitDtl")->name("roll.transit.dtl");
        Route::post("roll/transit/add", "addTransitRoll")->name("roll.transit.add");
        Route::post("roll/transit/book", "rollTransitBook")->name("roll.transit.book");
        Route::post("roll/transit/roll/stock", "rollTransitRollStock")->name("roll.transit.rll.stock");

        Route::get("roll/list/{flag?}", "rollList")->name("roll.list");
        Route::post("roll/add", "addRoll")->name("roll.add");
        Route::post("roll/swap", "swapRollNo")->name("roll.swap");
        Route::post("roll/swap/selected", "swapSelectedRoll")->name("roll.swap.selected");
        Route::post("roll/sell/selected", "sellRole")->name("roll.sell.selected");
        Route::post("roll/split", "splitRoll")->name("roll.split");
        Route::match(["get","post"],"roll/sell/generate-chalan","generateRollChalan")->name("roll.sell.generate.chalan");
        Route::get("roll/chalan/register", "chalanRegister")->name("roll.chalan.register");
        Route::get("roll/transport/dtlHtml/{id}","transPortDtlHtml")->name("roll.transport.dtlhtml");
        Route::get("roll/sell/register", "sellRegister")->name("roll.sell.register");
        Route::get("roll/sell/return/sell/{id}", "returnSell")->name("roll.sell.return");
        Route::post("roll/import", "importRoll")->name("roll.import");
        Route::get('roll/import-template', 'downloadCsvTemplate')->name('download.roll.import.template')->withoutMiddleware("auth:sanctum");
        Route::post("roll/book", "rollBook")->name("roll.book");
        Route::get("roll/dtl/{id}", "rollDtl")->name("roll.dtl");
        Route::get("roll/dtl-full/{id}", "rollDtlFull")->name("roll.dtl.full");
        Route::post("roll/schedule/printing", "rollPrintingSchedule")->name("roll.printing.schedule");
        Route::post("roll/printing/update", "rollPrintingUpdate")->name("roll.printing.update");
        Route::post("roll/printing/delete", "rollPrintingDelete")->name("roll.printing.delete");
        Route::post("roll/schedule/cutting", "rollCuttingSchedule")->name("roll.cutting.schedule");
        Route::post("roll/cutting/update", "rollCuttingUpdate")->name("roll.cutting.update");

        Route::get("roll/register", "rollRegister")->name("roll.register");
        Route::get("roll/loop-register", "loopRegister")->name("roll.loop.register");
        Route::get("roll/register/printing/{machineId?}", "rollRegisterPrinting")->name("roll.register.printing");
        Route::get("roll/register/cutting/{machineId?}", "rollRegisterCutting")->name("roll.register.cutting");
        Route::get("roll/schedule/{flag?}", "rollSchedule")->name("roll.schedule");
        Route::post("roll/schedule-set/{flag}", "rollScheduleSet")->name("roll.schedule.set");
        Route::post("roll/production/printing/updateWeight", "editPrintingWeight")->name("roll.production.edit.printing.weight");
        Route::get("roll/production/printing/{machineId?}", "rollProduction")->name("roll.production.printing");
        Route::get("roll/production/cutting/{machineId?}", "rollProductionCutting")->name("roll.production.cutting");

        Route::post("roll/search/printing", "rollSearchPrinting")->name("roll.search.printing");
        Route::post("roll/search/printing/list", "rollSearchPrintingRollList")->name("roll.search.printing.list");
        Route::post("roll/search/cutting", "rollSearchCutting")->name("roll.search.cutting");
        Route::post("roll/search/cutting/list", "rollSearchCuttingRollList")->name("roll.search.cutting.list");
        Route::post("roll/cutting/garbage/model", "getDataForCuttingGarbage")->name("roll.cutting.garbage.model");
        Route::post("roll/update/edit", "rollUpdate")->name("roll.update.edit");
    });

    Route::controller(ScheduleController::class)->group(function () {
        Route::get("schedule/printing/get/{machineId?}", "getRollForPrinting")->name("schedule.printing.get");
        Route::post("schedule/printing/save/{machineId?}", "savePrintingSchedule")->name("schedule.printing.save");
        Route::get("schedule/cutting/get/{machineId?}", "getRollForCutting")->name("schedule.cutting.get");
        Route::post("schedule/cutting/save/{machineId?}", "saveCuttingSchedule")->name("schedule.cutting.save");
    });

    Route::controller(PackingController::class)->group(function () {
        Route::get("packing/entry", "packingEnter")->name("packing.entry");
        Route::get("packing/wip", "WIPVerification")->name("packing.wip");
        Route::get("packing/serial", "getPackingSerialNo")->name("packing.serial");
        Route::post("packing/wip/delete", "deleteWIP")->name("packing.wip.delete");
        Route::post("packing/wip/disburse/order", "disburseOrder")->name("packing.wip.disburse.order");
        Route::post("packing/entry-wip/add", "packingEnterWipAdd")->name("packing.entry.wip.add");
        Route::post("packing/entry/search", "searchRoll")->name("packing.entry.search");
        Route::post("packing/entry/add", "packingEnterAdd")->name("packing.entry.add");
        Route::match(["get", "post"], "packing/stock", "bagStock")->name("packing.stock");
        Route::post("packing/godown/transport", "reivingTransport")->name("packing.godown.transport");
        Route::get("packing/inTransport", "addBagInTransport")->name("packing.inTransport");
        Route::post("packing/godown/add", "addInGodown")->name("packing.godown.add");
        Route::match(["get", "post"], "packing/godown/reiving/{godownTypeId}", "reivingGodown")->name("packing.godown.reiving");
        Route::match(["get", "post"], "packing/godown/{godownTypeId}", "bagGodown")->name("packing.godown");
        Route::get("packing/transport/for/{flag}", "bagTransport")->name("packing.transport.for");
        Route::get("packing/transport/stock", "bagStockToGodown")->name("packing.transport.stock");
        Route::post("packing/transport/search", "searchPackingForTransport")->name("packing.transport.search");
        Route::post("packing/transport/save", "transportAdd")->name("packing.transport.save");
        Route::get("packing/transport/register", "transportRegister")->name("packing.transport.register");
        Route::get("packing/transport/dtlHtml/{id}","transPortDtlHtml")->name("packing.transport.dtlhtml");
        Route::get("packing/chalan/register", "chalanRegister")->name("packing.chalan.register");
        Route::get("packing/transport/delete/{id}", "deleteTransPortDtl")->name("packing.transport.delete");
        Route::get("packing/transport/return/sell/{id}", "returnSell")->name("packing.transport.return.sell");
        Route::get("packing/bag/dtl/{id}","bagDtl")->name("packing.bag.dtl");
        Route::post("packing/bag/edit","editBag")->name("packing.bag.edit");
        Route::post("packing/bag/delete","deleteBag")->name("packing.bag.delete");
        Route::get("packing/bag/history", "bagHistory")->name("packing.bag.history");
        Route::post("packing/generate/chalan","generateChalan")->name("packing.generate.chalan");
        Route::get("packing/view/chalan/{unique_id}","viewChalan")->name("packing.view.chalan");
    });

    Route::controller(OtherRegister::class)->group(function () {
        Route::get("register/accept-garbage", "acceptGarbage")->name("register.accept.garbage");
        Route::get("register/loop-book-status", "loopStockBook")->name("register.loop.book.status");
    });

    Route::controller(ImportOldRecords::class)->group(function () {
        Route::match(["get", "post"], "import/order", "importOrders")->name("import.order");
        Route::match(["get", "post"], "import/order/roll/map", "orderRollMap")->name("import.order.roll.map");
        Route::match(["get", "post"], "import/despatch/history", "importDespatchHistory")->name("import.despatch.history");
        Route::match(["get","post"],"import/client","importClient");
        Route::match(["get","post"],"import/bag","importBag")->name("import.bag");
    });

    Route::prefix('report')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::match(["get", "post"], "/daily/production", "dailyProduction")->name("report.daily.production");
            Route::match(["get", "post"], "/repeated/order", "orderRepitition")->name("report.repeated.order");
            Route::match(["get", "post"], "/agent/order", "agentOrder")->name("report.agent.order");
            Route::get("/agent/order/dtl", "agentOrderDtl")->name("report.agent.order.dtl");
            Route::get("/legacy/client/order", "legacyClientOrder")->name("legacy.client.order");
            Route::get("/roll/shortage", "rollShortage")->name("roll.shortage");
            Route::get("garbage", "garbageRegister")->name("report.garbage");
            Route::get("roll/status", "rollStatus")->name("report.roll.status");
        });
    });

    Route::prefix("accounting")->group(function(){
        Route::controller(AccountingController::class)->group(function(){
            Route::get("/garbage/verification","garbageVerification")->name("accounting.garbage.verification");
            Route::post("/close/garbage","garbageClose")->name("accounting.close.garbage");
            Route::post("/wip/garbage/delete","deleteWIPGarbage")->name("accounting.wip.garbage.delete");
        });
    });

    Route::get("test/test",function(){
        if(request()->ajax()){
            dd(request()->all());
        }
        return view("testFom");
    });
});
