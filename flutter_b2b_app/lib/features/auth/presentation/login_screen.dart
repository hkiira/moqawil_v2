import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../domain/auth_controller.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  Future<void> _handleLogin() async {
    if (_phoneController.text.isEmpty || _passwordController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter phone and password')),
      );
      return;
    }

    FocusScope.of(context).unfocus();
    final success = await ref.read(authControllerProvider.notifier).login(
      _phoneController.text.trim(),
      _passwordController.text.trim(),
    );

    if (success && mounted) {
      context.go('/home');
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);
    final isLoading = authState is AsyncLoading;

    // Listen for errors
    ref.listen<AsyncValue>(authControllerProvider, (_, state) {
      if (!state.isLoading && state.hasError) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(state.error.toString()), backgroundColor: Colors.red),
        );
      }
    });

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          // Glassmorphism Backgrounds
          Positioned(
            top: -50,
            left: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 80, sigmaY: 80),
              child: Container(
                width: 250,
                height: 250,
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.primary.withOpacity(0.08),
                  shape: BoxShape.circle,
                ),
              ),
            ),
          ),
          Positioned(
            bottom: -50,
            right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 80, sigmaY: 80),
              child: Container(
                width: 300,
                height: 300,
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.secondary.withOpacity(0.08),
                  shape: BoxShape.circle,
                ),
              ),
            ),
          ),
          
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 32),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.business_center, size: 80, color: Theme.of(context).colorScheme.primary),
                    const SizedBox(height: 16),
                    Text('B2B Portal', style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Theme.of(context).colorScheme.onSurface)),
                    const SizedBox(height: 8),
                    Text('Sign in to your account', style: TextStyle(fontSize: 16, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7))),
                    const SizedBox(height: 48),

                    // Phone Field
                    _buildTextField(
                      context: context,
                      controller: _phoneController,
                      icon: Icons.phone,
                      hintText: 'Phone Number',
                      keyboardType: TextInputType.phone,
                    ),
                    const SizedBox(height: 16),

                    // Password Field
                    _buildTextField(
                      context: context,
                      controller: _passwordController,
                      icon: Icons.lock,
                      hintText: 'Password',
                      obscureText: _obscurePassword,
                      suffixIcon: IconButton(
                        icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
                        onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                      ),
                    ),
                    const SizedBox(height: 32),

                    // Login Button
                    SizedBox(
                      width: double.infinity,
                      height: 56,
                      child: ElevatedButton(
                        onPressed: isLoading ? null : _handleLogin,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Theme.of(context).colorScheme.primary,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          elevation: 2,
                        ),
                        child: isLoading
                            ? const CircularProgressIndicator(color: Colors.white)
                            : const Text('Sign In', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                      ),
                    ),
                    const SizedBox(height: 24),
                    
                    // Create Account Link
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text("Don't have an account?", style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7))),
                        TextButton(
                          onPressed: () => context.push('/signup'),
                          child: Text('Create Account', style: TextStyle(color: Theme.of(context).colorScheme.primary, fontWeight: FontWeight.bold)),
                        ),
                      ],
                    )
                  ],
                ),
              ),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildTextField({
    required BuildContext context,
    required TextEditingController controller,
    required IconData icon,
    required String hintText,
    bool obscureText = false,
    TextInputType? keyboardType,
    Widget? suffixIcon,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withOpacity(0.15)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: TextField(
        controller: controller,
        obscureText: obscureText,
        keyboardType: keyboardType,
        style: TextStyle(color: Theme.of(context).colorScheme.onSurface),
        decoration: InputDecoration(
          prefixIcon: Icon(icon, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
          suffixIcon: suffixIcon,
          hintText: hintText,
          hintStyle: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 20),
        ),
      ),
    );
  }
}
