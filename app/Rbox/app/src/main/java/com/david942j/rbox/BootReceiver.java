package com.david942j.rbox;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.NetworkInfo;
import android.net.wifi.WifiManager;
import android.util.Log;

/**
 * Created by david942j on 2015/1/15.
 */
public class BootReceiver extends BroadcastReceiver {
    @Override
    public void onReceive(Context context, Intent intent) {
        Log.w("OAO","ERR");
        NetworkInfo info = intent.getParcelableExtra(WifiManager.EXTRA_NETWORK_INFO);
        if(info != null && info.isConnected()) {
            Log.w("OAO","start");
            context.startService(new Intent(context, UploadService.class));
        }
        else {
            Log.w("OAO","stop");
            context.stopService(new Intent(context, UploadService.class));
        }
    }
}
