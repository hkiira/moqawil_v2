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
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: Theme.of(context).colorScheme.onSurface),
      ),
      body: Stack(
        children: [
          // Backgrounds
          Positioned(
            top: -100, right: -100,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          
          SafeArea(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Create Account', style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Theme.of(context).colorScheme.onSurface)),
                  const SizedBox(height: 8),
                  Text('Sign up to access the B2B portal', style: TextStyle(fontSize: 16, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7))),
                  const SizedBox(height: 32),

                  _buildTextField(context: context, controller: _nameController, icon: Icons.person, hintText: 'Full Name'),
                  const SizedBox(height: 16),
                  
                  _buildTextField(context: context, controller: _phoneController, icon: Icons.phone, hintText: 'Phone Number', keyboardType: TextInputType.phone),
                  const SizedBox(height: 16),
                  
                  _buildTextField(
                    context: context,
                    controller: _passwordController, icon: Icons.lock, hintText: 'Password', obscureText: _obscurePassword,
                    suffixIcon: IconButton(
                      icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
                      onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                    ),
                  ),
                  const SizedBox(height: 16),
                  
                  _buildTextField(context: context, controller: _referralController, icon: Icons.people, hintText: 'Referral Code (Optional)'),
                  const SizedBox(height: 24),

                  Text('Your Location', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Theme.of(context).colorScheme.onSurface)),
                  const SizedBox(height: 16),
                  
                  _buildTextField(context: context, controller: _addressController, icon: Icons.location_on, hintText: 'Street Address'),
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
                        backgroundColor: Theme.of(context).colorScheme.primary,
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
        controller: controller, obscureText: obscureText, keyboardType: keyboardType,
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
