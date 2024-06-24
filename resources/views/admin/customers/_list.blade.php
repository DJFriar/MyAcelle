@if ($customers->count() > 0)
    <table class="table table-box pml-table mt-2"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($customers as $key => $customer)
            <tr class="position-relative">
                <td width="1%" class="list-check-col">
                    <img width="50" class="rounded-circle me-2" src="{{ $customer->user->getProfileImageUrl() }}" alt="">
                </td>
                <td>
                    <h5 class="m-0 text-bold">
                        <a class="kq_search d-block" href="{{ action('Admin\CustomerController@edit', $customer->uid) }}">{{ $customer->displayName() }}</a>
                    </h5>
                    <span class="text-muted kq_search">{{ $customer->user->email }}</span><br>
                    <span class="text-muted kq_search">{{ trans('messages.customer.bounce_feedback_rate') }} <span title="{{ trans('messages.customer.bounce_feedback_rate_desc') }}" class="xtooltip">{{ number_to_percentage($customer->readCache('Bounce/FeedbackRate') ?? 0) }}</span></span>
                    <br />
                    <span class="text-muted2">{{ trans('messages.created_at') }}: {{ Auth::user()->admin->formatDateTime($customer->created_at, 'datetime_full') }}</span>
                </td>
                <td>
                    @if ($customer->getCurrentActiveGeneralSubscription())
                        <h5 class="no-margin stat-num">
                            <span><i class="material-symbols-rounded">assignment_turned_in</i> {{ $customer->getCurrentActiveGeneralSubscription()->planGeneral->name }}</span>
                        </h5>
                        <span class="text-muted2">{{ trans('messages.current_plan') }}</span>
                    @elseif ($customer->getNewGeneralSubscription())
                        <h5 class="no-margin stat-num">
                            <a href="{{ action('Admin\PlanController@general', $customer->getNewGeneralSubscription()->planGeneral->uid) }}">
                                <span><i class="material-symbols-rounded">assignment_turned_in</i> {{ $customer->getNewGeneralSubscription()->getPlanName() }}</span>
                            </a>
                        </h5>
                        <span class="text-muted2">{{ trans('messages.subscription.pending') }}</span>
                    @else
                        <span class="text-muted2">{{ trans('messages.customer.no_active_subscription') }}</span>
                    @endif
                </td>
                <td class="stat-fix-size">
                    @if ($customer->getCurrentActiveGeneralSubscription())
                        <div class="d-flex">
                            <div class="single-stat-box pull-left me-5">
                                <span class="no-margin text-primary stat-num">{{ number_with_delimiter($customer->getCurrentActiveGeneralSubscription()->getRemainingEmailCreditsPercentage()*100) }}%</span>
                                <div class="progress progress-xxs">
                                    <div class="progress-bar progress-bar-info" style="width: {{ $customer->getCurrentActiveGeneralSubscription()->getRemainingEmailCreditsPercentage()*100 }}%">
                                    </div>
                                </div>
                                <span class="text-muted">
                                    <strong>{{ number_with_delimiter($customer->getCurrentActiveGeneralSubscription()->getSendEmailRateTracker()->getCreditsUsed()) }}/{{ ($customer->getCurrentActiveGeneralSubscription()->getCreditsLimit('send') == -1) ? '∞' : number_with_delimiter($customer->getCurrentActiveGeneralSubscription()->getCreditsLimit('send')) }}</strong>
                                    <div class="text-nowrap">{{ trans('messages.sending_credits_used') }}</div>
                                </span>
                            </div>
                            <div class="single-stat-box pull-left">
                                <span class="no-margin text-primary stat-num">{{ $customer->displaySubscribersUsage() }}</span>
                                <div class="progress progress-xxs">
                                    <div class="progress-bar progress-bar-info" style="width: {{ $customer->readCache('SubscriberUsage') }}%">
                                    </div>
                                </div>
                                <span class="text-muted"><strong>{{ number_with_delimiter($customer->readCache('SubscriberCount')) }}/{{ number_with_delimiter($customer->maxSubscribers()) }}</strong>
                                <br /> {{ trans('messages.subscribers') }}</span>
                            </div>
                        </div>
                    @endif
                </td>
                <td>
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-{{ $customer->status }}">{{ trans('messages.user_status_' . $customer->status) }}</span>
                    </span>
                </td>
                <td class="text-end">
                    @can('loginAs', $customer)
                        <a href="{{ action('Admin\CustomerController@loginAs', $customer->uid) }}" data-popup="tooltip"
                            title="{{ trans('messages.login_as_this_customer') }}" role="button"
                            class="btn btn-primary btn-icon"><span class="material-symbols-rounded">login</span></a>
                    @endcan
                    @can('update', $customer)
                        <a href="{{ action('Admin\CustomerController@edit', $customer->uid) }}"
                            data-popup="tooltip" title="{{ trans('messages.edit') }}"
                            role="button" class="btn btn-secondary btn-icon"><span class="material-symbols-rounded">edit</span></a>
                    @endcan
                    @if (Auth::user()->can('delete', $customer) ||
                        Auth::user()->can('enable', $customer) ||
                        Auth::user()->can('disable', $customer) ||
                        Auth::user()->can('assignPlan', $customer)
                    )
                        <div class="btn-group">
                            <button role="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('assignPlan', $customer)
                                    <li>
                                        <a
                                            href="{{ action('Admin\CustomerController@assignPlan', [
                                                "uid" => $customer->uid,
                                            ]) }}"
                                            class="dropdown-item assign_plan_button"
                                        >
                                            <i class="material-symbols-rounded">assignment_turned_in</i>
                                             {{ trans('messages.customer.assign_plan') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('enable', $customer)
                                    <li>
                                        <a class="dropdown-item list-action-single" link-confirm="{{ trans('messages.enable_customers_confirm') }}" href="{{ action('Admin\CustomerController@enable', ["uids" => $customer->uid]) }}">
                                            <span class="material-symbols-rounded">play_arrow</span> {{ trans('messages.enable') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('disable', $customer)
                                    <li>
                                        <a class="dropdown-item list-action-single" link-confirm="{{ trans('messages.disable_customers_confirm') }}" href="{{ action('Admin\CustomerController@disable', ["uids" => $customer->uid]) }}">
                                            <span class="material-symbols-rounded">hide_source</span> {{ trans('messages.disable') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('read', $customer)
                                    <li>
                                        <a class="dropdown-item" href="{{ action('Admin\CustomerController@subscriptions', $customer->uid) }}">
                                            <span class="material-symbols-rounded">assignment_turned_in</span> {{ trans('messages.subscriptions') }}
                                        </a>
                                    </li>
                                @endcan
                                <li>
                                    <a list-action="one-click-login" class="dropdown-item" href="{{ action('Admin\CustomerController@oneClickLogin', $customer->uid) }}">
                                        <span class="material-symbols-rounded">link</span> {{ trans('messages.admin.generate_one_click_login') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item list-action-single" link-confirm="{{ trans('messages.delete_users_confirm') }}" href="{{ action('Admin\CustomerController@delete', ['uids' => $customer->uid]) }}">
                                        <span class="material-symbols-rounded">delete_outline</span> {{ trans('messages.delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ["items" => $customers])
    
    <script>
        var AdminCustomersList = {
            oneClickPopup: null,

            init: function() {
                this.oneClickPopup = new Popup();
            }
        }

        $(function() {
            AdminCustomersList.init();

            $('[list-action="one-click-login"]').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                AdminCustomersList.oneClickPopup.load(url);
            })
        })
    </script>

    <script>
        var assignPlan;        
        $(function() {
            $('.assign_plan_button').click(function(e) {
                e.preventDefault();

                var src = $(this).attr('href');
                assignPlan = new Popup({
                    url: src
                });

                assignPlan.load();
            });
        });
    </script>

@elseif (!empty(request()->keyword))
    <div class="empty-list">
        <span class="material-symbols-rounded">people_outline</span>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <span class="material-symbols-rounded">people_outline</span>
        <span class="line-1">
            {{ trans('messages.customer_empty_line_1') }}
        </span>
    </div>
@endif
