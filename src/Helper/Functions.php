<?php
if (!function_exists('dispatcher_server')) {
    /**
     * @return \Swoft\Http\Server\DispatcherServer
     */
    function dispatcher_server()
    {
        return \Swoft\App::getBean('dispatcherServer');
    }
}
