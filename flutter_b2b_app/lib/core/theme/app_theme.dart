import 'package:flutter/material.dart';

class AppColors {
  static const Color primary = Color(0xFF1E4D2B);      // Deep Mint Green
  static const Color secondary = Color(0xFFD4AF37);    // Golden Amber
  static const Color background = Color(0xFFF9F9F6);   // Warm off-white
  static const Color surface = Color(0xFFF9F9F6);      // Warm off-white
  
  static const Color textDark = Color(0xFF1A1C19);     // High contrast dark text
  static const Color textMuted = Color(0xFF5C5F5A);    // Secondary/Muted text
  static const Color border = Color(0xFFE2E4DF);       // Soft border for light theme
}

class AppTheme {
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      primaryColor: AppColors.primary,
      scaffoldBackgroundColor: AppColors.background,
      colorScheme: const ColorScheme.light(
        primary: AppColors.primary,
        secondary: AppColors.secondary,
        surface: AppColors.surface,
        onPrimary: Colors.white,
        onSecondary: Colors.black,
        onSurface: AppColors.textDark,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      bottomNavigationBarTheme: const BottomNavigationBarThemeData(
        backgroundColor: AppColors.surface,
        selectedItemColor: AppColors.primary,
        unselectedItemColor: AppColors.textMuted,
        elevation: 8,
      ),
      textTheme: const TextTheme(
        headlineLarge: TextStyle(color: AppColors.textDark, fontWeight: FontWeight.bold),
        headlineMedium: TextStyle(color: AppColors.textDark, fontWeight: FontWeight.bold),
        titleLarge: TextStyle(color: AppColors.textDark, fontWeight: FontWeight.bold),
        titleMedium: TextStyle(color: AppColors.textDark),
        bodyLarge: TextStyle(color: AppColors.textDark),
        bodyMedium: TextStyle(color: AppColors.textDark),
      ),
    );
  }
}
