<?php

namespace Acelle\Http\Controllers\Admin;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;

use SMTPValidateEmail\Validator as SmtpEmailValidator;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            // Email address to verify
            $email = $request->input('email');

            // Pass it to the Validator
            $emails    = [ $email ];
            $sender    = 'nghi@b-teka.com';
            $validator = new SmtpEmailValidator($emails, $sender);
            // $validator->debug = true;
            $results   = $validator->validate();

            // Get MX records to print out
            $mxs = '';
            foreach($results['domains'] as $key => $info) {
                $mxs .= "<br><strong>{$key}</strong><br>";
                foreach($info['mxs'] as $name => $num) {
                    $mxs .= "+ {$name} - {$num}<br>";
                }
            }

            if ($results[$email]) {
                $results['success'] = true;
            } else {
                $results['success'] = false;
            }

            // Show the verification result above the input form
            return view('admin.verify.index', ['results' => $results, 'mxs' => $mxs, 'email' => $email]);
        } else {
            // Show the input form
            return view('admin.verify.index');
        }

    }
}
