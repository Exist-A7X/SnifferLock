package com.tonikamitv.loginregister;

import com.android.volley.Response;
import com.android.volley.toolbox.StringRequest;

import java.util.HashMap;
import java.util.Map;

public class RegisterRequest extends StringRequest {
    private static final String REGISTER_REQUEST_URL = "http://snifferlock.tk/androidRegistration.php";
    private Map<String, String> params;

    public RegisterRequest(String username, String email, String password, String confirmPassword, Response.Listener<String> listener) {
        super(Method.POST, REGISTER_REQUEST_URL, listener, null);
        params = new HashMap<>();
        params.put("username", username);
        params.put("emailAddress", email);
        params.put("password", password);
        params.put("confirmPassword", confirmPassword);

    }

    @Override
    public Map<String, String> getParams() {
        return params;
    }
}
