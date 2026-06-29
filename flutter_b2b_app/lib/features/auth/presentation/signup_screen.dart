import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import '../domain/auth_controller.dart';

class SignupScreen extends ConsumerStatefulWidget {
  const SignupScreen({super.key});

  @override
  ConsumerState<SignupScreen> createState() => _SignupScreenState();
}

class _SignupScreenState extends ConsumerState<SignupScreen> {
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();
  final _passwordController = TextEditingController();
  final _referralController = TextEditingController();
  
  bool _obscurePassword = true;
  double _latitude = 0.0;
  double _longitude = 0.0;
  
  GoogleMapController? _mapController;
  Set<Marker> _markers = {};

  @override
  void initState() {
    super.initState();
    _determinePosition();
  }

  Future<void> _determinePosition() async {
    bool serviceEnabled;
    LocationPermission permission;

    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) return;

    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) return;
    }
    
    if (permission == LocationPermission.deniedForever) return; 

    Position position = await Geolocator.getCurrentPosition();
    _updateLocation(position.latitude, position.longitude);
  }

  Future<void> _updateLocation(double lat, double lng) async {
    setState(() {
      _latitude = lat;
      _longitude = lng;
      _markers = {
        Marker(markerId: const MarkerId('current'), position: LatLng(lat, lng))
      };
    });
    
    _mapController?.animateCamera(CameraUpdate.newLatLngZoom(LatLng(lat, lng), 15));

    try {
      List<Placemark> placemarks = await placemarkFromCoordinates(lat, lng);
      if (placemarks.isNotEmpty) {
        Placemark place = placemarks[0];
        setState(() {
          _addressController.text = '${place.street}, ${place.locality}, ${place.country}';
        });
      }
    } catch (e) {
      // Ignore geocoding errors
    }
  }

  Future<void> _handleSignup() async {
    if (_phoneController.text.isEmpty || _passwordController.text.isEmpty || _nameController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Name, Phone and Password are required')),
      );
      return;
    }

    FocusScope.of(context).unfocus();
    final data = {
      'name': _nameController.text.trim(),
      'phone': _phoneController.text.trim(),
      'password': _passwordController.text.trim(),
      'adresse': _addressController.text.trim(),
      'latitude': _latitude,
      'longitude': _longitude,
      'referral': _referralController.text.trim(),
    };

    final success = await ref.read(authControllerProvider.notifier).signup(data);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Account created! Please log in.'), backgroundColor: Colors.green),
      );
      context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);
    final isLoading = authState is AsyncLoading;

    ref.listen<AsyncValue>(authControllerProvider, (_, state) {
      if (!state.isLoading && state.hasError) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(state.error.toString()), backgroundColor: Colors.red),
        );
      }
    });

    return Scaffold(
      backgroundColor: const Color(0xFF1E1E2C),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Stack(
        children: [
          // Backgrounds
          Positioned(
            top: -100, right: -100,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: const Color(0xFF0D9488).withOpacity(0.3), shape: BoxShape.circle)),
            ),
          ),
          
          SafeArea(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Create Account', style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Colors.white)),
                  const SizedBox(height: 8),
                  const Text('Sign up to access the B2B portal', style: TextStyle(fontSize: 16, color: Colors.white70)),
                  const SizedBox(height: 32),

                  _buildTextField(controller: _nameController, icon: Icons.person, hintText: 'Full Name'),
                  const SizedBox(height: 16),
                  
                  _buildTextField(controller: _phoneController, icon: Icons.phone, hintText: 'Phone Number', keyboardType: TextInputType.phone),
                  const SizedBox(height: 16),
                  
                  _buildTextField(
                    controller: _passwordController, icon: Icons.lock, hintText: 'Password', obscureText: _obscurePassword,
                    suffixIcon: IconButton(
                      icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Colors.white54),
                      onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                    ),
                  ),
                  const SizedBox(height: 16),
                  
                  _buildTextField(controller: _referralController, icon: Icons.people, hintText: 'Referral Code (Optional)'),
                  const SizedBox(height: 24),

                  const Text('Your Location', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                  const SizedBox(height: 16),
                  
                  _buildTextField(controller: _addressController, icon: Icons.location_on, hintText: 'Street Address'),
                  const SizedBox(height: 16),

                  // Map
                  ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: SizedBox(
                      height: 200,
                      child: GoogleMap(
                        initialCameraPosition: const CameraPosition(target: LatLng(0, 0), zoom: 2),
                        markers: _markers,
                        onMapCreated: (controller) => _mapController = controller,
                        onTap: (pos) => _updateLocation(pos.latitude, pos.longitude),
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 32),

                  SizedBox(
                    width: double.infinity,
                    height: 56,
                    child: ElevatedButton(
                      onPressed: isLoading ? null : _handleSignup,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF4F46E5),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      ),
                      child: isLoading
                          ? const CircularProgressIndicator(color: Colors.white)
                          : const Text('Sign Up', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                    ),
                  ),
                  const SizedBox(height: 32),
                ],
              ),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildTextField({required TextEditingController controller, required IconData icon, required String hintText, bool obscureText = false, TextInputType? keyboardType, Widget? suffixIcon}) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(16),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
        child: Container(
          decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), border: Border.all(color: Colors.white.withOpacity(0.2)), borderRadius: BorderRadius.circular(16)),
          child: TextField(
            controller: controller, obscureText: obscureText, keyboardType: keyboardType,
            style: const TextStyle(color: Colors.white),
            decoration: InputDecoration(
              prefixIcon: Icon(icon, color: Colors.white54), suffixIcon: suffixIcon, hintText: hintText,
              hintStyle: const TextStyle(color: Colors.white54), border: InputBorder.none, contentPadding: const EdgeInsets.symmetric(vertical: 20),
            ),
          ),
        ),
      ),
    );
  }
}
