import 'package:http/http.dart' as http;

import '../core/config/api.dart';

class AuthService {
  Future<void> login(String username, String password) async {
    var response = await http.post(
      Uri.parse("${Api.baseUrl}/login"),

      body: {"username": username, "password": password},
    );

    print(response.body);
  }
}
