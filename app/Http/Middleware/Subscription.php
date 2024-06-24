<?php

namespace Acelle\Http\Middleware;

use Closure;

class Subscription
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!config('app.saas')) {
            return $next($request);
        }

        // init
        $activeSubscription = $request->user()->customer->getCurrentActiveSubscription(); // get current active subscription

        // Các trường hợp xảy ra:
        // 1. Chưa có subscription (ko có record subscription trong DB)
        //     null
        // 2. Có subscription nhưng đã:
        //     Ended
        //     Cancelled
        //     Terminated
        // 3. Có subscription new
        // 4. Có subscription new và đang pending for approval
        // 5. Có active subscription

        // Case 1, 2, 3, 4: Chưa có current active subscription thì trả về subscription steps. Ở đây tiếp tục xử lý các bước dự vào từng case
        if (!$activeSubscription) {
            return redirect()->action('SubscriptionController@index');
        }

        // Case 5: Có active subscription thì tiếp tục...

        // Kiểm tra plan đang subscribe hiện tại không active
        if (!$activeSubscription->planGeneral->isActive()) {
            return response()->view('errors.general', [ 'message' => "Current subscribed plan [{$activeSubscription->planGeneral->name}] is not active. Ask administrator for more information!" ]);
        }

        // If sending server not available
        if ($activeSubscription->planGeneral->useSystemSendingServer()) {
            $server = $activeSubscription->planGeneral->primarySendingServer();
            if (is_null($server)) {
                return response()->view('errors.general', [ 'message' => __('messages.plan.sending_server.no_sending_server_error', ['plan' => $activeSubscription->planGeneral->name]) ]);
            }
        }

        return $next($request);
    }
}
