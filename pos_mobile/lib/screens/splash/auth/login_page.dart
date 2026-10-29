import 'package:flutter/material.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {

  final TextEditingController usernameController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();

  bool isHidePassword = true;

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      backgroundColor: Colors.grey.shade100,

      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(

            padding: const EdgeInsets.all(25),

            child: Column(

              children: [

                const Icon(
                  Icons.store,
                  size: 90,
                  color: Colors.blue,
                ),

                const SizedBox(height: 15),

                const Text(
                  "POS MOBILE",
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                  ),
                ),

                const SizedBox(height: 8),

                Text(
                  "Silakan login untuk melanjutkan",
                  style: TextStyle(
                    color: Colors.grey.shade600,
                  ),
                ),

                const SizedBox(height: 40),

                TextField(
                  controller: usernameController,

                  decoration: InputDecoration(

                    labelText: "Username",

                    prefixIcon: const Icon(Icons.person),

                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),

                  ),
                ),

                const SizedBox(height: 20),

                TextField(

                  controller: passwordController,

                  obscureText: isHidePassword,

                  decoration: InputDecoration(

                    labelText: "Password",

                    prefixIcon: const Icon(Icons.lock),

                    suffixIcon: IconButton(

                      onPressed: () {

                        setState(() {

                          isHidePassword = !isHidePassword;

                        });

                      },

                      icon: Icon(

                        isHidePassword
                            ? Icons.visibility
                            : Icons.visibility_off,

                      ),

                    ),

                    border: OutlineInputBorder(

                      borderRadius: BorderRadius.circular(12),

                    ),

                  ),

                ),

                const SizedBox(height: 35),

                SizedBox(

                  width: double.infinity,

                  height: 50,

                  child: ElevatedButton(

                    onPressed: () {

                      print(usernameController.text);

                      print(passwordController.text);

                    },

                    child: const Text(
                      "LOGIN",
                      style: TextStyle(
                        fontSize: 18,
                      ),
                    ),

                  ),

                ),

              ],

            ),

          ),
        ),
      ),
    );
  }
}