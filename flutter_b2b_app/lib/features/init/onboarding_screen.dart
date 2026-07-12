import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  final List<Map<String, String>> _onboardingData = [
    {
      "title": "Welcome to Moqa B2B",
      "subtitle": "Discover the ultimate wholesale catalog tailored for your business needs.",
      "image": "assets/images/onboarding1.png",
      "icon": "storefront"
    },
    {
      "title": "Seamless Shopping",
      "subtitle": "Add products to your cart, save favorites to your wishlist, and checkout effortlessly.",
      "image": "assets/images/onboarding2.png",
      "icon": "shopping_cart"
    },
    {
      "title": "Secure & Fast",
      "subtitle": "Enjoy secure transactions and real-time order tracking.",
      "image": "assets/images/onboarding3.png",
      "icon": "security"
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          // Background blobs
          Positioned(
            top: -100, left: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          Positioned(
            bottom: -50, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 250, height: 250, decoration: BoxDecoration(color: Theme.of(context).colorScheme.secondary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              children: [
                Expanded(
                  child: PageView.builder(
                    controller: _pageController,
                    onPageChanged: (index) {
                      setState(() {
                        _currentPage = index;
                      });
                    },
                    itemCount: _onboardingData.length,
                    itemBuilder: (context, index) {
                      return Padding(
                        padding: const EdgeInsets.all(40.0),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              _getIcon(_onboardingData[index]['icon']!),
                              size: 120,
                              color: Theme.of(context).colorScheme.primary,
                            ),
                            const SizedBox(height: 60),
                            Text(
                              _onboardingData[index]['title']!,
                              style: TextStyle(
                                color: Theme.of(context).colorScheme.onSurface,
                                fontSize: 32,
                                fontWeight: FontWeight.bold,
                              ),
                              textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 20),
                            Text(
                              _onboardingData[index]['subtitle']!,
                              style: TextStyle(
                                color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7),
                                fontSize: 18,
                                height: 1.5,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(
                    _onboardingData.length,
                    (index) => AnimatedContainer(
                      duration: const Duration(milliseconds: 300),
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      height: 8,
                      width: _currentPage == index ? 24 : 8,
                      decoration: BoxDecoration(
                        color: _currentPage == index ? Theme.of(context).colorScheme.primary : Theme.of(context).colorScheme.onSurface.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 40),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 40),
                  child: SizedBox(
                    width: double.infinity,
                    height: 56,
                    child: ElevatedButton(
                      onPressed: () {
                        if (_currentPage == _onboardingData.length - 1) {
                          context.go('/login');
                        } else {
                          _pageController.nextPage(
                            duration: const Duration(milliseconds: 300),
                            curve: Curves.easeInOut,
                          );
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Theme.of(context).colorScheme.primary,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      child: Text(
                        _currentPage == _onboardingData.length - 1 ? 'Get Started' : 'Next',
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ],
      ),
    );
  }

  IconData _getIcon(String name) {
    switch (name) {
      case 'storefront':
        return Icons.storefront;
      case 'shopping_cart':
        return Icons.shopping_cart;
      case 'security':
        return Icons.security;
      default:
        return Icons.star;
    }
  }
}
