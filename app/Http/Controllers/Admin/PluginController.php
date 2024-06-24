<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;
use Acelle\Model\Plugin;
use Exception;

class PluginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->user()->admin->can('read', new \Acelle\Model\Plugin())) {
            return $this->notAuthorized();
        }

        // If admin can view all sending domains
        if (!$request->user()->admin->can("readAll", new \Acelle\Model\Plugin())) {
            $request->merge(array("admin_id" => $request->user()->admin->id));
        }

        // exlude customer seding plugins
        $request->merge(array("no_customer" => true));

        $plugins = \Acelle\Model\Plugin::search($request);

        return view('admin.plugins.index2', [
            'plugins' => $plugins
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        if (!$request->user()->admin->can('read', new \Acelle\Model\Plugin())) {
            return $this->notAuthorized();
        }

        // If admin can view all sending domains
        if (!$request->user()->admin->can("readAll", new \Acelle\Model\Plugin())) {
            $request->merge(array("admin_id" => $request->user()->admin->id));
        }

        // exlude customer seding plugins
        $request->merge(array("no_customer" => true));

        $plugins = \Acelle\Model\Plugin::search($request)->paginate($request->per_page);

        // Do preliminary check before loading the plugins list
        // For example, in case any plugin cannot be loaded, record the related error
        $settingUrls = [];
        $blacklist = [];
        foreach ($plugins as $plugin) {
            // Check if there is any error is recorded by the autoloader
            $error = $plugin->getPluginInfo('error');
            if (!is_null($error)) {
                $blacklist[$plugin->name] = 'CANNOT LOAD PLUGIN. Message: '.$error;
                continue;
            }

            // Generate setting buttons
            try {
                $composerJson = $plugin->getComposerJson();
                if (array_key_exists('extra', $composerJson) && array_key_exists('setting-route', $composerJson['extra'])) {
                    $url = action($composerJson['extra']['setting-route']);
                    $settingUrls[$plugin->name] = $url;
                } else {
                    throw new Exception('extra/setting-route not found');
                }
            } catch (Exception $ex) {
                $blacklist[$plugin->name] = 'Something went wrong with the plugin: '.$ex->getMessage();
            }
        }

        return view('admin.plugins._list', [
            'plugins' => $plugins,
            'settingUrls' => $settingUrls,
            'blacklist' => $blacklist,
        ]);
    }

    /**
     * Install/Upgrage plugins.
     *
     * @return \Illuminate\Http\Response
     */
    public function install(Request $request)
    {
        // authorize
        if (!$request->user()->admin->can('install', Plugin::class)) {
            return $this->notAuthorized();
        }

        // do install
        if ($request->isMethod('post')) {
            // Upload
            $pluginName = Plugin::upload($request);

            // Install Plugin
            Plugin::installFromDir($pluginName);

            return response()->json([ 'url' => action('Admin\PluginController@index') ]);
        }

        return view('admin.plugins.install');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        $plugin = Plugin::findByUid($request->uid);

        if (!$request->user()->admin->can('delete', $plugin)) {
            return $this->notAuthorized();
        }

        if ($request->isMethod('post')) {
            $pluginName = $plugin->name;
            $plugin->deleteAndCleanup();

            return view('admin.plugins.deleted', [
                'pluginName' => $pluginName,
            ]);
        }

        return view('admin.plugins.delete', [
            'plugin' => $plugin,
        ]);

        // $items = \Acelle\Model\Plugin::whereIn(
        //     'uid',
        //     is_array($request->uids) ? $request->uids : explode(',', $request->uids)
        // );

        // foreach ($items->get() as $item) {
        //     // authorize
        //     if ($request->user()->admin->can('delete', $item)) {
        //         $item->deleteAndCleanup();
        //     }
        // }

        // if(!$request->ajax()){
        //     return redirect()->action('Admin\PluginController@index')
        //         ->with('alert-success',  trans('messages.plugins.deleted'));
        // }

        // // Redirect to my lists page
        // echo trans('messages.plugins.deleted');
    }

    /**
     * Disable sending server.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $items = Plugin::whereIn(
            'uid',
            is_array($request->uids) ? $request->uids : explode(',', $request->uids)
        );

        foreach ($items->get() as $item) {
            // authorize
            if ($request->user()->admin->can('disable', $item)) {
                $item->disable();
            }
        }

        if (!$request->ajax()) {
            return redirect()->back()
                ->with('alert-success', trans('messages.plugins.disabled'));
        }

        // Redirect to my lists page
        echo trans('messages.plugins.disabled');
    }

    /**
     * Disable sending server.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $plugins = Plugin::whereIn(
            'uid',
            is_array($request->uids) ? $request->uids : explode(',', $request->uids)
        );

        foreach ($plugins->get() as $plugin) {
            // authorize
            if ($request->user()->admin->can('enable', $plugin)) {
                $plugin->activate();
            }
        }

        if (!$request->ajax()) {
            return redirect()->back()
                ->with('alert-success', trans('messages.plugins.enabled'));
        }

        // Redirect to my lists page
        echo trans('messages.plugins.enabled');
    }

    /**
     * Email verification server display options form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function options(Request $request, $uid = null)
    {
        if ($uid) {
            $plugin = \Acelle\Model\Plugin::findByUid($uid);
        } else {
            $plugin = new \Acelle\Model\Plugin($request->all());
            $options = $plugin->getOptions();
        }

        return view('admin.plugins._options', [
            'server' => $plugin,
            'options' => $options,
        ]);
    }

    public function reindex(Request $request)
    {
        Plugin::resetPluginMasterFile();
        echo "done";
    }
}
