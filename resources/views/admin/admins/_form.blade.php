<div class="row">
	<div class="col-md-3">
		<div class="sub_section">
			<h2 class="text-semibold text-primary">{{ trans('messages.profile_photo') }}</h2>
			<div class="media profile-image">
				<div class="media-left">
					<a href="#" class="upload-media-container">
						<img preview-for="image" empty-src="{{ AppUrl::asset('images/placeholder.jpg') }}" src="{{ $admin->user->getProfileImageUrl() }}" class="rounded-circle" alt="">
					</a>
					<input type="file" name="image" class="file-styled previewable hide">
					<input type="hidden" name="_remove_image" value='' />
				</div>
				<div class="media-body text-center">
					<h5 class="media-heading text-semibold">{{ trans('messages.upload_photo') }}</h5>
					{{ trans('messages.photo_at_least', ["size" => "300px x 300px"]) }}
					<br /><br />
					<a href="#upload" onclick="$('input[name=image]').trigger('click')" class="btn btn-primary me-1"><span class="material-symbols-rounded">file_download</span> {{ trans('messages.upload') }}</a>
					<a href="#remove" class="btn btn-secondary remove-profile-image"><span class="material-symbols-rounded">delete_outline</span> {{ trans('messages.remove') }}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="sub_section">
			<h2 class="text-semibold text-primary">{{ trans('messages.account') }}</h2>

			@include('helpers.form_control', ['type' => 'text', 'name' => 'email', 'value' => $admin->user->email, 'help_class' => 'profile', 'rules' => $admin->user->rules()])

			@include('helpers.form_control', ['type' => 'select',
											'name' => 'admin_group_id',
											'label' => trans('messages.group'),
											'value' => $admin->admin_group_id,
											'options' => Acelle\Model\AdminGroup::getSelectOptions(Auth::user()->admin),
											'include_blank' => trans('messages.choose'),
											'rules' => $admin->user->rules()
										])

			@include('helpers.form_control', ['type' => 'password', 'label'=> trans('messages.new_password'), 'name' => 'password', 'rules' => $admin->user->rules()])

			@include('helpers.form_control', ['type' => 'password', 'name' => 'password_confirmation', 'rules' => $admin->user->rules()])

		</div>
	</div>
	<div class="col-md-5">
		<div class="sub_section">
			<h2 class="text-semibold text-primary">{{ trans('messages.basic_information') }}</h2>

            @if (get_localization_config('show_last_name_first', Auth::user()->admin->getLanguageCode()))
                <div class="row">
                    <div class="col-md-6">
                        @include('helpers.form_control', ['type' => 'text', 'name' => 'last_name', 'value' => $admin->user->last_name, 'rules' => $admin->user->rules()])
                    </div>
                    <div class="col-md-6">
                        @include('helpers.form_control', ['type' => 'text', 'name' => 'first_name', 'value' => $admin->user->first_name, 'rules' => $admin->user->rules()])
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        @include('helpers.form_control', ['type' => 'text', 'name' => 'first_name', 'value' => $admin->user->first_name, 'rules' => $admin->user->rules()])
                    </div>
                    <div class="col-md-6">
                        @include('helpers.form_control', ['type' => 'text', 'name' => 'last_name', 'value' => $admin->user->last_name, 'rules' => $admin->user->rules()])
                    </div>
                </div>
            @endif


            @if (config('custom.japan'))
                <input type="hidden" name="timezone" value="Asia/Tokyo" />
            @else
                @include('helpers.form_control', [
                    'type' => 'select',
                    'name' => 'timezone',
                    'value' => $admin->timezone ?? config('app.timezone'),
                    'options' => Tool::getTimezoneSelectOptions(),
                    'include_blank' => trans('messages.choose'),
                    'rules' => $admin->user->rules()
                ])
            @endif
			

            @include('helpers.form_control', [
                'type' => 'select',
                'name' => 'language_id',
                'label' => trans('messages.language'),
                'value' => $admin->language_id ?? \Acelle\Model\Language::getDefaultLanguage()->id,
                'options' => Acelle\Model\Language::getSelectOptions(),
                'include_blank' => trans('messages.choose'),
                'rules' => $admin->user->rules()
            ])
			

			@if (!$admin->hasCustomerAccount())
                <div class="form-group">
                    <div class="d-flex" style="width:100%">
                        <div class="me-5">
                            <label class="text-semibold">
                                {{ trans('messages.create_customer_account') }}
                            </label>
                            <p class="checkbox-description mt-1 mb-0">
                                {{ trans('messages.admin.create_customer_account.help') }}
                            </p>
                        </div>
                            
                        <div class="d-flex align-items-top">
                            @include('helpers.switch', [
                                'name' => 'create_customer_account',
                                'option' => 'yes',
                                'unchecked_option' => 'no',
                                'value' => 'yes',
                            ])
                        </div>
                    </div>
                </div>
			@endif
		</div>
	</div>

</div>
<hr />
<div class="text-end">
	<button class="btn btn-secondary"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
</div>

<script>
	$(function() {
		// Preview upload image
		$("input.previewable").on('change', function() {
			var img = $("img[preview-for='" + $(this).attr("name") + "']");
			previewImageBrowse(this, img);
		});
		$(".remove-profile-image").click(function() {
			var img = $(this).parents(".profile-image").find("img");
			var imput = $(this).parents(".profile-image").find("input[name='_remove_image']");
			img.attr("src", img.attr("empty-src"));
			imput.val("true");
		});
	});
</script>