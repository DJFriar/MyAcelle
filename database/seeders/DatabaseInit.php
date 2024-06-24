<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseInit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // languages
        $this->insertLanguages();

        // // layouts
        $this->insertLayouts();

        // // countries
        $this->insertCountries();

        // // admin_groups
        $this->insertAdminGroups();

        // plans
        $this->insertPlans();
    }

    public function insertLanguages()
    {
        $dataItems = [
            [
                'name' => 'English',
                'code' => 'en',
                'region_code' => 'us',
                'status' => \Acelle\Model\Language::STATUS_ACTIVE,
                'is_default' => true,
            ],
            [
                'name' => 'Spanish',
                'code' => 'es',
                'region_code' => 'es',
                'status' => \Acelle\Model\Language::STATUS_ACTIVE,
                'is_default' => false,
            ],
        ];

        foreach ($dataItems as $dataItem) {
            \Acelle\Model\Language::create($dataItem);
        }
    }

    public function insertLayouts()
    {
        $headers = ['alias', 'subject', 'group_name', 'content', 'type'];
        $dataItems = [
            ['sign_up_form', 'Sign up', 'Sign-up', '	<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- subscribe form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						                                                                        <h4>Welcome to {LIST_NAME}</h4>                        <hr>                        {FIELDS}                        <br>                        {SUBSCRIBE_BUTTON}					</div>                    <!-- /subscribe form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page', '2016-06-19 00:54:49', '2016-06-19 00:54:49'],
            ['sign_up_thankyou_page', 'Thank you', 'Sign-up', '<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- subscribe form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						                                                                        <h4>Almost finished...</h4>                        <hr>						<p>We need to confirm your email address.</p>						<p>To complete the subscription process, please click the link in the email we just sent you.						</p>					</div>                    <!-- /subscribe form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page'],
            ['sign_up_confirmation_email', 'Sign-up confirmation', 'Sign-up', '<!-- Page container --><div class="page-container login-container" style="min-height: 249px;"><!-- Page content --><div class="page-content"><!-- Main content --><div class="content-wrapper"><div class="row"><div class="col-sm-2 col-md-3"></div><div class="col-sm-8 col-md-6"><!-- subscribe form --><h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2><div class="panel panel-body"><h4>Please Confirm Subscription</h4><hr />Click the link below to confirm your subscription:<br /> {SUBSCRIBE_CONFIRM_URL}<hr /><p>If you received this email by mistake, simply delete it. You won\'t be subscribed if you don\'t click the confirmation link above.</p></div><!-- /subscribe form --></div></div></div><!-- /main content --></div><!-- /page content --> <!-- Footer --><div class="footer text-white">&copy; 2020. <span class="text-white">Copyright &copy; 2023. All rights reserved.</a></div><!-- /footer --></div><!-- /page container -->', 'email'],
            ['sign_up_confirmation_thankyou', 'Thank you', 'Sign-up', '<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- subscribe form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						                                                                        <h4>Subscription Confirmed</h4>                        <hr>						<p>Your subscription to our list has been confirmed.</p><p>Thank you for subscribing!</p>						                     <a href="{UPDATE_PROFILE_URL}" class="btn btn-info bg-teal-800">Manage your preferences</a>					</div>                    <!-- /subscribe form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page'],
            ['sign_up_welcome_email', 'Welcome', 'Sign-up', '<!-- Page container --><div class="page-container login-container" style="min-height: 249px;"><!-- Page content --><div class="page-content"><!-- Main content --><div class="content-wrapper"><div class="row"><div class="col-sm-2 col-md-3"></div><div class="col-sm-8 col-md-6"><!-- subscribe form --><h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2><div class="panel panel-body"><h4>Your subscription to our list has been confirmed.</h4><hr /><p>For your records, here is a copy of the information you submitted to us...</p>{SUBSCRIBER_SUMMARY}<hr /><p>If at any time you wish to stop receiving our emails, you can: <br /> <a href="{UNSUBSCRIBE_URL}" class="btn btn-info bg-teal-800">Unsubscribe here</a></p></div><!-- /subscribe form --></div></div></div><!-- /main content --></div><!-- /page content --> <!-- Footer --><div class="footer text-white">&copy; 2020. <span class="text-white">Copyright &copy; 2023. All rights reserved.</a></div><!-- /footer --></div><!-- /page container -->', 'email'],
            ['unsubscribe_form', 'Unsubscribe', 'Unsubscribe', '<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						                                                                        <h4>Unsubscribe</h4>                        <hr>                        <p>You are going to opt out of mail list {LIST_NAME}, please click the button below to confirm. Really sorry to let you go!</p>                                                <p>{UNSUBSCRIBE_BUTTON}</p>					</div>                    <!-- /form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page'],
            ['unsubscribe_success_page', 'Unsubscribed', 'Unsubscribe', '	<!-- Page container -->\n	<div class="page-container login-container" style="min-height:249px">\n\n		<!-- Page content -->\n		<div class="page-content">\n\n			<!-- Main content -->\n			<div class="content-wrapper">\n				<div class="row">\n					<div class="col-sm-2 col-md-3">\n						\n					</div>\n					<div class="col-sm-8 col-md-6">\n\n                    <!-- form -->\n				                  \n					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>\n                        <div class="panel panel-body">						\n                                                \n                        <h4>Unsubscribe Successful</h4>\n                        <hr>\n                        <p>You have been removed from {LIST_NAME}.</p>\n                        \n                        <br />\n					</div>\n\n                    <!-- /form -->\n    \n						\n					</div>\n				</div>\n			</div>\n			<!-- /main content -->\n\n		</div>\n		<!-- /page content -->\n\n\n		<!-- Footer -->\n		<div class="footer text-white">\n			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>			\n		</div>\n		<!-- /footer -->\n\n	</div>\n	<!-- /page container -->', 'page'],
            ['unsubscribe_goodbye_email', 'Unsubscribed', 'Unsubscribe', '	<!-- Page container -->\n	<div class="page-container login-container" style="min-height:249px">\n\n		<!-- Page content -->\n		<div class="page-content">\n\n			<!-- Main content -->\n			<div class="content-wrapper">\n				<div class="row">\n					<div class="col-sm-2 col-md-3">\n						\n					</div>\n					<div class="col-sm-8 col-md-6">\n\n                    <!-- form -->\n				                  \n					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>\n                        <div class="panel panel-body">						\n                                                \n                        <h4>We have removed your email address from our list.</h4>\n                        <hr>\n                        <p>We\'re sorry to see you go.</p>\n                        <p>Was this a mistake? Did you forward one of our emails to a friend, and they clicked the unsubscribe link not realizing they were in fact unsubscribing you from this list? If this was a mistake, you can re-subscribe at: <br />\n<a href="{SUBSCRIBE_URL}" class="btn btn-info bg-teal-800">Subscribe</a>\n</p>				</div>\n\n                    <!-- /form -->\n    \n						\n					</div>\n				</div>\n			</div>\n			<!-- /main content -->\n\n		</div>\n		<!-- /page content -->\n\n\n		<!-- Footer -->\n		<div class="footer text-white">\n			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>			\n		</div>\n		<!-- /footer -->\n\n	</div>\n	<!-- /page container -->', 'email'],
            ['profile_update_email', 'Update profile', 'Update profile', '	<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						                                                                        <p>We received a request to change your subscription preferences for List 1.</p><p>If you made this request, and would like to change your preferences, use the link below</p><p><a href="{UPDATE_PROFILE_URL}">Update your preferences</a></p><p>If you did not make this request, it was probably submitted by someone else by mistake. You can ignore this email, and no changes will be made to your subscription preferences.</p>				</div>                    <!-- /form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'email'],
            ['profile_update_form', 'Update profile', 'Update profile', '	<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						<h4>Update your preferences</h4><hr />                        {FIELDS}<br />{UPDATE_PROFILE_BUTTON} or <a class="btn btn-info bg-grey" href="{UNSUBSCRIBE_URL}">Unsubscribe</a>					</div>                    <!-- /form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page'],
            ['profile_update_success_page', 'Update profile', 'Update profile', '<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- subscribe form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						                                                                        <h4>Profile Updated</h4>                        <hr>						<p>Your profile information has been updated. For your records, here is a copy of the information you submitted to us...</p>{SUBSCRIBER_SUMMARY}			</div>                    <!-- /subscribe form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page'],
            ['profile_update_email_sent', 'Update profile', 'Update profile', '	<!-- Page container -->	<div class="page-container login-container" style="min-height:249px">		<!-- Page content -->		<div class="page-content">			<!-- Main content -->			<div class="content-wrapper">				<div class="row">					<div class="col-sm-2 col-md-3">											</div>					<div class="col-sm-8 col-md-6">                    <!-- form -->				                  					<h2 class="text-semibold mt-40 text-white">{LIST_NAME}</h2>                        <div class="panel panel-body">						<h4>Email sent</h4>          <hr />                        <p>For security, we\'ve sent an email to your inbox that contains a link to update your preferences.</p></div>                    <!-- /form -->    											</div>				</div>			</div>			<!-- /main content -->		</div>		<!-- /page content -->		<!-- Footer -->		<div class="footer text-white">			<span class="text-white">Copyright &copy; 2023. All rights reserved.</a>					</div>		<!-- /footer -->	</div>	<!-- /page container -->', 'page'],
            ['registration_confirmation_email', 'Registration confirmation', 'Subscription', '<!DOCTYPE html><html><head></head><body><h2>Hello {CUSTOMER_NAME}, welcome aboard</h2><div class="page-container login-container" style="min-height: 249px;"><div class="page-content"><div class="content-wrapper"><div class="row"><div class="col-sm-8 col-md-6"><div class="panel panel-body"><h4>Please Confirm&nbsp;Registration</h4><hr />Click the link below to activate your account:<br /><a href="{ACTIVATION_URL}">{ACTIVATION_URL}</a><br /><hr /><p>&nbsp;</p></div></div></div></div></div><div class="footer text-white"><div class="footer text-muted">&copy; 2023. Email Marketing Application</div></div><!-- /footer --></div><!-- /page container --><p>&nbsp;</p></body></html>', 'email', null, '2017-02-23 07:44:26'],
        ];

        foreach ($dataItems as $dataItem) {
            $data = [];

            // mapping headers
            foreach ($headers as $index => $header) {
                $data[$header] = $dataItem[$index];
            }

            // create
            \Acelle\Model\Layout::create($data);
        }
    }

    public function insertCountries()
    {
        $headers = ['name', 'code', 'status'];

        $dataItems = [
            ['Afghanistan', 'AF', 'active'],
            ['Albania', 'AL', 'active'],
            ['Algeria', 'DZ', 'active'],
            ['American Samoa', 'AS', 'active'],
            ['Andorra', 'AD', 'active'],
            ['Angola', 'AO', 'active'],
            ['Anguilla', 'AI', 'active'],
            ['Antigua', 'AG', 'active'],
            ['Argentina', 'AR', 'active'],
            ['Armenia', 'AM', 'active'],
            ['Aruba', 'AW', 'active'],
            ['Australia', 'AU', 'active'],
            ['Austria', 'AT', 'active'],
            ['Azerbaijan', 'AZ', 'active'],
            ['Bahrain', 'BH', 'active'],
            ['Bangladesh', 'BD', 'active'],
            ['Barbados', 'BB', 'active'],
            ['Belarus', 'BY', 'active'],
            ['Belgium', 'BE', 'active'],
            ['Belize', 'BZ', 'active'],
            ['Benin', 'BJ', 'active'],
            ['Bermuda', 'BM', 'active'],
            ['Bhutan', 'BT', 'active'],
            ['Bolivia', 'BO', 'active'],
            ['Bosnia and Herzegovina', 'BA', 'active'],
            ['Botswana', 'BW', 'active'],
            ['Brazil', 'BR', 'active'],
            ['British Indian Ocean Territory', 'IO', 'active'],
            ['British Virgin Islands', 'VG', 'active'],
            ['Brunei', 'BN', 'active'],
            ['Bulgaria', 'BG', 'active'],
            ['Burkina Faso', 'BF', 'active'],
            ['Burma Myanmar', 'MM', 'active'],
            ['Burundi', 'BI', 'active'],
            ['Cambodia', 'KH', 'active'],
            ['Cameroon', 'CM', 'active', '2016-07-13 21:42:44', '2016-07-13 21:42:44'],
            ['Canada', 'CA', 'active', '2016-07-13 21:42:44', '2016-07-13 21:42:44'],
            ['Cape Verde', 'CV', 'active', '2016-07-13 21:42:44', '2016-07-13 21:42:44'],
            ['Cayman Islands', 'KY', 'active', '2016-07-13 21:42:44', '2016-07-13 21:42:44'],
            ['Central African Republic', 'CF', 'active', '2016-07-13 21:42:44', '2016-07-13 21:42:44'],
            ['Chad', 'TD', 'active', '2016-07-13 21:42:44', '2016-07-13 21:42:44'],
            ['Chile', 'CL', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['China', 'CN', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Colombia', 'CO', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Comoros', 'KM', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Cook Islands', 'CK', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Costa Rica', 'CR', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Côte d\'Ivoire', 'CI', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Croatia', 'HR', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Cuba', 'CU', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Cyprus', 'CY', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Czech Republic', 'CZ', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Democratic Republic of Congo', 'CD', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Denmark', 'DK', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Djibouti', 'DJ', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Dominica', 'DM', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Dominican Republic', 'DO', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Ecuador', 'EC', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Egypt', 'EG', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['El Salvador', 'SV', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Equatorial Guinea', 'GQ', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Eritrea', 'ER', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Estonia', 'EE', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Ethiopia', 'ET', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Falkland Islands', 'FK', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Faroe Islands', 'FO', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Federated States of Micronesia', 'FM', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Fiji', 'FJ', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Finland', 'FI', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['France', 'FR', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['French Guiana', 'GF', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['French Polynesia', 'PF', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Gabon', 'GA', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Georgia', 'GE', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Germany', 'DE', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Ghana', 'GH', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Gibraltar', 'GI', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Greece', 'GR', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Greenland', 'GL', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Grenada', 'GD', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Guadeloupe', 'GP', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Guam', 'GU', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Guatemala', 'GT', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Guinea', 'GN', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Guinea-Bissau', 'GW', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Guyana', 'GY', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Haiti', 'HT', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Honduras', 'HN', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Hong Kong', 'HK', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Hungary', 'HU', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Iceland', 'IS', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['India', 'IN', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Indonesia', 'ID', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Iran', 'IR', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Iraq', 'IQ', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Ireland', 'IE', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Israel', 'IL', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Italy', 'IT', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Jamaica', 'JM', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Japan', 'JP', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Jordan', 'JO', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Kazakhstan', 'KZ', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Kenya', 'KE', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Kiribati', 'KI', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Kosovo', 'XK', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Kuwait', 'KW', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Kyrgyzstan', 'KG', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Laos', 'LA', 'active', '2016-07-13 21:42:45', '2016-07-13 21:42:45'],
            ['Latvia', 'LV', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Lebanon', 'LB', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Lesotho', 'LS', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Liberia', 'LR', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Libya', 'LY', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Liechtenstein', 'LI', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Lithuania', 'LT', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Luxembourg', 'LU', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Macau', 'MO', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Macedonia', 'MK', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Madagascar', 'MG', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Malawi', 'MW', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Malaysia', 'MY', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Maldives', 'MV', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mali', 'ML', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Malta', 'MT', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Marshall Islands', 'MH', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Martinique', 'MQ', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mauritania', 'MR', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mauritius', 'MU', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mayotte', 'YT', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mexico', 'MX', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Moldova', 'MD', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Monaco', 'MC', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mongolia', 'MN', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Montenegro', 'ME', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Montserrat', 'MS', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Morocco', 'MA', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Mozambique', 'MZ', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Namibia', 'NA', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Nauru', 'NR', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Nepal', 'NP', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Netherlands', 'NL', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Netherlands Antilles', 'AN', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['New Caledonia', 'NC', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['New Zealand', 'NZ', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Nicaragua', 'NI', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Niger', 'NE', 'active', '2016-07-13 21:42:46', '2016-07-13 21:42:46'],
            ['Nigeria', 'NG', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Niue', 'NU', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Norfolk Island', 'NF', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['North Korea', 'KP', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Northern Mariana Islands', 'MP', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Norway', 'NO', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Oman', 'OM', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Pakistan', 'PK', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Palau', 'PW', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Palestine', 'PS', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Panama', 'PA', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Papua New Guinea', 'PG', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Paraguay', 'PY', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Peru', 'PE', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Philippines', 'PH', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Poland', 'PL', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Portugal', 'PT', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Puerto Rico', 'PR', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Qatar', 'QA', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Republic of the Congo', 'CG', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Réunion', 'RE', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Romania', 'RO', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Russia', 'RU', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Rwanda', 'RW', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Saint Barthélemy', 'BL', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Saint Helena', 'SH', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Saint Kitts and Nevis', 'KN', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Saint Martin', 'MF', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Saint Pierre and Miquelon', 'PM', 'active', '2016-07-13 21:42:47', '2016-07-13 21:42:47'],
            ['Saint Vincent and the Grenadines', 'VC', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Samoa', 'WS', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['San Marino', 'SM', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['São Tomé and Príncipe', 'ST', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Saudi Arabia', 'SA', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Senegal', 'SN', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Serbia', 'RS', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Seychelles', 'SC', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Sierra Leone', 'SL', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Singapore', 'SG', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Slovakia', 'SK', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Slovenia', 'SI', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Solomon Islands', 'SB', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Somalia', 'SO', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['South Africa', 'ZA', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['South Korea', 'KR', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Spain', 'ES', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Sri Lanka', 'LK', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['St. Lucia', 'LC', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Sudan', 'SD', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Suriname', 'SR', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Swaziland', 'SZ', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Sweden', 'SE', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Switzerland', 'CH', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Syria', 'SY', 'active', '2016-07-13 21:42:48', '2016-07-13 21:42:48'],
            ['Taiwan', 'TW', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Tajikistan', 'TJ', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Tanzania', 'TZ', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Thailand', 'TH', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['The Bahamas', 'BS', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['The Gambia', 'GM', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Timor-Leste', 'TL', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Togo', 'TG', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Tokelau', 'TK', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Tonga', 'TO', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Trinidad and Tobago', 'TT', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Tunisia', 'TN', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Turkey', 'TR', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Turkmenistan', 'TM', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Turks and Caicos Islands', 'TC', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Tuvalu', 'TV', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Uganda', 'UG', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Ukraine', 'UA', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['United Arab Emirates', 'AE', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['United Kingdom', 'GB', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['United States', 'US', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Uruguay', 'UY', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['US Virgin Islands', 'VI', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Uzbekistan', 'UZ', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Vanuatu', 'VU', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Vatican City', 'VA', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Venezuela', 'VE', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Vietnam', 'VN', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Wallis and Futuna', 'WF', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Yemen', 'YE', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Zambia', 'ZM', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
            ['Zimbabwe', 'ZW', 'active', '2016-07-13 21:42:49', '2016-07-13 21:42:49'],
        ];

        foreach ($dataItems as $dataItem) {
            $data = [];

            // mapping headers
            foreach ($headers as $index => $header) {
                $data[$header] = $dataItem[$index];
            }

            // create
            \Acelle\Model\Country::create($data);
        }
    }

    public function insertAdminGroups()
    {
        $headers = ['name', 'options', 'permissions', 'creator_id'];
        $dataItems = [
            ['Administrator', '', '{"admin_group_read":"all","admin_group_create":"yes","admin_group_update":"all","admin_group_delete":"all","admin_read":"all","admin_create":"yes","admin_update":"all","admin_delete":"all","admin_login_as":"all","customer_read":"all","customer_create":"yes","customer_update":"all","customer_delete":"all","customer_login_as":"all","subscription_read":"all","subscription_create":"yes","subscription_update":"all","subscription_disable":"all","subscription_enable":"all","subscription_delete":"all","subscription_paid":"all","subscription_unpaid":"all","plan_read":"all","plan_create":"yes","plan_update":"all","plan_delete":"all","payment_method_read":"all","payment_method_create":"yes","payment_method_update":"all","payment_method_delete":"all","sending_server_read":"all","sending_server_create":"yes","sending_server_update":"all","sending_server_delete":"all","bounce_handler_read":"all","bounce_handler_create":"yes","bounce_handler_update":"all","bounce_handler_delete":"all","fbl_handler_read":"all","fbl_handler_create":"yes","fbl_handler_update":"all","fbl_handler_delete":"all","sending_domain_read":"all","sending_domain_create":"yes","sending_domain_update":"all","sending_domain_delete":"all","template_read":"all","template_create":"yes","template_update":"all","template_delete":"all","layout_read":"yes","layout_update":"yes","setting_general":"yes","setting_sending":"yes","setting_system_urls":"yes","setting_access_when_offline":"yes","setting_background_job":"yes","setting_upgrade_manager":"yes","language_read":"yes","language_create":"yes","language_update":"yes","language_delete":"yes","currency_read":"all","currency_create":"yes","currency_update":"all","currency_delete":"all","report_blacklist":"yes","report_tracking_log":"yes","report_bounce_log":"yes","report_feedback_log":"yes","report_open_log":"yes","report_click_log":"yes","report_unsubscribe_log":"yes"}', null],
            ['Reseller', '', '{"admin_group_read":"no","admin_group_create":"no","admin_group_update":"no","admin_group_delete":"no","admin_read":"no","admin_create":"no","admin_update":"no","admin_delete":"no","admin_login_as":"no","customer_read":"own","customer_create":"yes","customer_update":"own","customer_delete":"own","customer_login_as":"own","subscription_read":"own","subscription_create":"yes","subscription_update":"no","subscription_disable":"own","subscription_enable":"own","subscription_delete":"own","subscription_paid":"no","subscription_unpaid":"no","plan_read":"all","plan_create":"no","plan_update":"no","plan_delete":"no","payment_method_read":"no","payment_method_create":"no","payment_method_update":"no","payment_method_delete":"no","sending_server_read":"no","sending_server_create":"no","sending_server_update":"no","sending_server_delete":"no","bounce_handler_read":"no","bounce_handler_create":"no","bounce_handler_update":"no","bounce_handler_delete":"no","fbl_handler_read":"no","fbl_handler_create":"no","fbl_handler_update":"no","fbl_handler_delete":"no","sending_domain_read":"no","sending_domain_create":"no","sending_domain_update":"no","sending_domain_delete":"no","template_read":"own","template_create":"yes","template_update":"own","template_delete":"own","layout_read":"no","layout_update":"no","setting_general":"no","setting_sending":"no","setting_system_urls":"no","setting_access_when_offline":"no","setting_background_job":"no","setting_upgrade_manager":"no","language_read":"no","language_create":"no","language_update":"no","language_delete":"no","currency_read":"no","currency_create":"no","currency_update":"no","currency_delete":"no","report_blacklist":"no","report_tracking_log":"no","report_bounce_log":"no","report_feedback_log":"no","report_open_log":"no","report_click_log":"no","report_unsubscribe_log":"no"}', null],
        ];

        foreach ($dataItems as $dataItem) {
            $data = [];

            // mapping headers
            foreach ($headers as $index => $header) {
                $data[$header] = $dataItem[$index];
            }

            // create
            \Acelle\Model\AdminGroup::create($data);
        }
    }

    public function insertPlans()
    {
        $headers = ['currency_id', 'name', 'price', 'frequency_amount', 'frequency_unit', 'options', 'status', 'created_at', 'updated_at', 'description', 'type'];
        $dataItems = [
            [1, 'Free', '0.00', '1', 'month', json_encode(array ( 'email_max' => '5000', 'list_max' => '10', 'subscriber_max' => '1000', 'subscriber_per_list_max' => '-1', 'segment_per_list_max' => '3', 'campaign_max' => '20', 'automation_max' => '10', 'billing_cycle' => 'monthly', 'sending_limit' => 'unlimited', 'sending_quota' => 1000, 'sending_quota_time' => 1, 'sending_quota_time_unit' => 'hour', 'max_process' => '1', 'all_sending_servers' => 'yes', 'max_size_upload_total' => '500', 'max_file_size_upload' => '5', 'unsubscribe_url_required' => 'yes', 'access_when_offline' => 'no', 'create_sending_domains' => 'no', 'sending_servers_max' => '-1', 'sending_domains_max' => '-1', 'all_email_verification_servers' => 'yes', 'create_email_verification_servers' => 'no', 'email_verification_servers_max' => '-1', 'list_import' => 'yes', 'list_export' => 'yes', 'all_sending_server_types' => 'yes', 'sending_server_types' => array ( 'amazon-smtp' => 'yes', 'amazon-api' => 'yes', 'sendgrid-smtp' => 'yes', 'sendgrid-api' => 'yes', 'mailgun-api' => 'yes', 'mailgun-smtp' => 'yes', 'elasticemail-api' => 'yes', 'elasticemail-smtp' => 'yes', 'sparkpost-api' => 'yes', 'sparkpost-smtp' => 'yes', 'smtp' => 'yes', 'sendmail' => 'yes', 'php-mail' => 'yes', ), 'sending_server_option' => 'system', 'sending_server_subaccount_uid' => NULL, 'api_access' => 'yes', 'email_footer_enabled' => 'yes', 'email_footer_trial_period_only' => 'no', 'html_footer' => '', 'plain_text_footer' => '', 'payment_gateway' => '', )), 'inactive', '2017-03-06 13:33:12', '2020-01-14 07:59:58', 'All the basics for businesses or individual to get started with email marketing', 'general'],
            [1, 'Standard', '250.00', '1', 'month', json_encode(array ( 'email_max' => '100000', 'list_max' => '50', 'subscriber_max' => '50000', 'subscriber_per_list_max' => '-1', 'segment_per_list_max' => '3', 'campaign_max' => '40', 'automation_max' => '20', 'billing_cycle' => 'monthly', 'sending_limit' => 'unlimited', 'sending_quota' => 1000, 'sending_quota_time' => 1, 'sending_quota_time_unit' => 'hour', 'max_process' => '1', 'all_sending_servers' => 'yes', 'max_size_upload_total' => '10000', 'max_file_size_upload' => '50', 'unsubscribe_url_required' => 'yes', 'access_when_offline' => 'no', 'create_sending_domains' => 'no', 'sending_servers_max' => '-1', 'sending_domains_max' => '-1', 'all_email_verification_servers' => 'yes', 'create_email_verification_servers' => 'no', 'email_verification_servers_max' => '-1', 'list_import' => 'yes', 'list_export' => 'yes', 'all_sending_server_types' => 'yes', 'sending_server_types' => array ( 'amazon-smtp' => 'yes', 'amazon-api' => 'yes', 'sendgrid-smtp' => 'yes', 'sendgrid-api' => 'yes', 'mailgun-api' => 'yes', 'mailgun-smtp' => 'yes', 'elasticemail-api' => 'yes', 'elasticemail-smtp' => 'yes', 'sparkpost-api' => 'yes', 'sparkpost-smtp' => 'yes', 'smtp' => 'yes', 'sendmail' => 'yes', 'php-mail' => 'yes', ), 'sending_server_option' => 'system', 'sending_server_subaccount_uid' => NULL, 'api_access' => 'yes', 'email_footer_enabled' => 'yes', 'email_footer_trial_period_only' => 'no', 'html_footer' => '', 'plain_text_footer' => '', 'payment_gateway' => '', )), 'inactive', '2017-03-06 13:33:12', '2020-01-14 08:01:05', 'Powerful statistics & insight report for maximized sales & marketing performance', 'general'],
            [1, 'Premium', '895.00', '1', 'month', json_encode(array ( 'email_max' => '1000000', 'list_max' => '-1', 'subscriber_max' => '-1', 'subscriber_per_list_max' => '-1', 'segment_per_list_max' => '3', 'campaign_max' => '-1', 'automation_max' => '-1', 'billing_cycle' => 'monthly', 'sending_limit' => 'unlimited', 'sending_quota' => 1000, 'sending_quota_time' => 1, 'sending_quota_time_unit' => 'hour', 'max_process' => '1', 'all_sending_servers' => 'yes', 'max_size_upload_total' => '50000', 'max_file_size_upload' => '100', 'unsubscribe_url_required' => 'yes', 'access_when_offline' => 'no', 'create_sending_domains' => 'yes', 'sending_servers_max' => '5', 'sending_domains_max' => '-1', 'all_email_verification_servers' => 'yes', 'create_email_verification_servers' => 'no', 'email_verification_servers_max' => '-1', 'list_import' => 'yes', 'list_export' => 'yes', 'all_sending_server_types' => 'yes', 'sending_server_types' => array ( 'amazon-smtp' => 'yes', 'amazon-api' => 'yes', 'sendgrid-smtp' => 'yes', 'sendgrid-api' => 'yes', 'mailgun-api' => 'yes', 'mailgun-smtp' => 'yes', 'elasticemail-api' => 'yes', 'elasticemail-smtp' => 'yes', 'sparkpost-api' => 'yes', 'sparkpost-smtp' => 'yes', 'smtp' => 'yes', 'sendmail' => 'yes', 'php-mail' => 'yes', ), 'sending_server_option' => 'system', 'sending_server_subaccount_uid' => NULL, 'api_access' => 'yes', 'email_footer_enabled' => 'yes', 'email_footer_trial_period_only' => 'no', 'html_footer' => '', 'plain_text_footer' => '', 'payment_gateway' => '', )), 'inactive', '2017-03-06 13:43:09', '2020-01-14 08:01:32', 'Advanced features for professionals who need unlimited marketing capability', 'general'],
            [1, 'Essentials', '19.00', '1', 'month', json_encode(array ( 'email_max' => '1000', 'list_max' => '2', 'subscriber_max' => '5000', 'subscriber_per_list_max' => '1000', 'segment_per_list_max' => '0', 'campaign_max' => '10', 'automation_max' => '10', 'billing_cycle' => 'monthly', 'sending_limit' => 'unlimited', 'sending_quota' => 100, 'sending_quota_time' => 1, 'sending_quota_time_unit' => 'minute', 'max_process' => '1', 'all_sending_servers' => 'yes', 'max_size_upload_total' => '200', 'max_file_size_upload' => '5', 'unsubscribe_url_required' => 'yes', 'access_when_offline' => 'no', 'create_sending_domains' => 'yes', 'sending_servers_max' => '-1', 'sending_domains_max' => '-1', 'all_email_verification_servers' => 'yes', 'create_email_verification_servers' => 'no', 'email_verification_servers_max' => '-1', 'list_import' => 'yes', 'list_export' => 'yes', 'all_sending_server_types' => 'yes', 'sending_server_types' => array ( 'amazon-smtp' => 'yes', 'amazon-api' => 'yes', 'sendgrid-smtp' => 'yes', 'sendgrid-api' => 'yes', 'mailgun-api' => 'yes', 'mailgun-smtp' => 'yes', 'elasticemail-api' => 'yes', 'elasticemail-smtp' => 'yes', 'sparkpost-api' => 'yes', 'sparkpost-smtp' => 'yes', 'smtp' => 'yes', 'sendmail' => 'yes', 'php-mail' => 'yes', ), 'sending_server_option' => 'system', 'sending_server_subaccount_uid' => NULL, 'api_access' => 'no', 'email_footer_enabled' => 'yes', 'email_footer_trial_period_only' => 'no', 'html_footer' => '', 'plain_text_footer' => '', 'payment_gateway' => '', )), 'inactive', '2019-06-05 19:52:09', '2020-01-14 08:00:12', 'Must-have features for marketing agency & individual to engage in email marketing', 'general'],

        ];

        foreach ($dataItems as $dataItem) {
            $data = [];

            // mapping headers
            foreach ($headers as $index => $header) {
                $data[$header] = $dataItem[$index];
            }

            // create
            \Acelle\Model\Plan::create($data);
        }

    }
}
