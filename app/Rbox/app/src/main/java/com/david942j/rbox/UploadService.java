package com.david942j.rbox;

import android.app.Service;
import android.content.Intent;
import android.os.Environment;
import android.os.FileObserver;
import android.os.IBinder;
import android.util.Log;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.HttpMultipartMode;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.MultipartEntityBuilder;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;

import java.io.File;

/**
 * Created by david942j on 2015/1/14.
 */
public class UploadService extends Service {
    final String path = Environment.getExternalStorageDirectory().toString()+"/DCIM/100MEDIA/";
    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        MediaMonitor m = new MediaMonitor(path);
        m.startWatching();
        return START_STICKY;
    }
    private void sendFile(String filename) {
        final String url = "http://192.168.1.22/rbox/index.php/files/upload";
        FileBody file = new FileBody(new File(path+filename));
        HttpClient client = new DefaultHttpClient();
        HttpPost post = new HttpPost(url);
        try {
            // setup multipart entity
            MultipartEntity entity = new MultipartEntity(HttpMultipartMode.BROWSER_COMPATIBLE);
            entity.addPart("file", file);

            post.setEntity(entity);

            // create response handler
            ResponseHandler<String> handler = new BasicResponseHandler();
            // execute and get response
            String result = new String(client.execute(post, handler).getBytes(), HTTP.UTF_8);
            Log.w("Result",result);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    private class MediaMonitor extends FileObserver {
        public MediaMonitor(String path) {
            super(path);
        }
        @Override
        public void onEvent(int event, String filename) {
            if(event == FileObserver.CREATE) {
                final String file = filename;
                new Thread() {
                    public void run() {
                        Log.w("Create file", file);
                        sendFile(file);
                    }
                }.start();
            }
        }
    }
}
