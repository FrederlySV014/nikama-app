import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

// ============================================================
// NIKAMA — Paleta de colores y tema
// Color principal: Amarillo (#F5C518), Negro (#0D0D0D), Blanco (#FAFAFA)
// Fuente: Inter via google_fonts (sin archivos locales)
// ============================================================

class NikamaColors {
  // Primarios
  static const Color primary = Color(0xFFF5C518);      // Amarillo Nikama
  static const Color primaryDark = Color(0xFFDBA500);   // Amarillo oscuro (hover)
  static const Color primaryLight = Color(0xFFFFF3CC);  // Amarillo claro (fondo sutil)

  // Neutros
  static const Color black = Color(0xFF0D0D0D);
  static const Color darkGray = Color(0xFF1A1A1A);
  static const Color mediumGray = Color(0xFF3D3D3D);
  static const Color lightGray = Color(0xFFB0B0B0);
  static const Color white = Color(0xFFFAFAFA);
  static const Color offWhite = Color(0xFFF5F5F5);
  static const Color surfaceWhite = Color(0xFFFFFFFF);

  // Estado
  static const Color success = Color(0xFF22C55E);
  static const Color error = Color(0xFFEF4444);
  static const Color warning = Color(0xFFFF9500);
  static const Color info = Color(0xFF3B82F6);

  // Degradados
  static const LinearGradient primaryGradient = LinearGradient(
    colors: [Color(0xFFF5C518), Color(0xFFDBA500)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient darkGradient = LinearGradient(
    colors: [Color(0xFF1A1A1A), Color(0xFF0D0D0D)],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );

  static const LinearGradient heroGradient = LinearGradient(
    colors: [Color(0xFF0D0D0D), Color(0xFF1F1A05)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
}

class NikamaTheme {
  static ThemeData get lightTheme {
    final interTextTheme = GoogleFonts.interTextTheme();

    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      primaryColor: NikamaColors.primary,
      scaffoldBackgroundColor: NikamaColors.offWhite,
      textTheme: interTextTheme,

      colorScheme: const ColorScheme.light(
        primary: NikamaColors.primary,
        onPrimary: NikamaColors.black,
        secondary: NikamaColors.black,
        onSecondary: NikamaColors.white,
        surface: NikamaColors.surfaceWhite,
        onSurface: NikamaColors.black,
        error: NikamaColors.error,
      ),

      // AppBar
      appBarTheme: AppBarTheme(
        backgroundColor: NikamaColors.surfaceWhite,
        foregroundColor: NikamaColors.black,
        elevation: 0,
        centerTitle: true,
        titleTextStyle: GoogleFonts.inter(
          fontSize: 18,
          fontWeight: FontWeight.w700,
          color: NikamaColors.black,
        ),
        iconTheme: const IconThemeData(color: NikamaColors.black),
      ),

      // Botones elevados
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: NikamaColors.primary,
          foregroundColor: NikamaColors.black,
          elevation: 0,
          shape: const RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(14)),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          textStyle: GoogleFonts.inter(
            fontSize: 16,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),

      // Botones outlined
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: NikamaColors.black,
          side: const BorderSide(color: NikamaColors.black, width: 1.5),
          shape: const RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(14)),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          textStyle: GoogleFonts.inter(
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      // Input Fields
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: NikamaColors.surfaceWhite,
        hintStyle: GoogleFonts.inter(
          color: NikamaColors.lightGray,
          fontSize: 14,
        ),
        labelStyle: GoogleFonts.inter(
          color: NikamaColors.mediumGray,
          fontSize: 14,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE0E0E0), width: 1.5),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: NikamaColors.primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: NikamaColors.error, width: 1.5),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: NikamaColors.error, width: 2),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      ),

      // Cards
      cardTheme: const CardThemeData(
        color: NikamaColors.surfaceWhite,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.all(Radius.circular(16)),
          side: BorderSide(color: Color(0xFFEEEEEE), width: 1),
        ),
        margin: EdgeInsets.zero,
      ),

      // Dividers
      dividerTheme: const DividerThemeData(
        color: Color(0xFFEEEEEE),
        thickness: 1,
        space: 1,
      ),

      // BottomNavigationBar
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        backgroundColor: NikamaColors.surfaceWhite,
        selectedItemColor: NikamaColors.primary,
        unselectedItemColor: NikamaColors.lightGray,
        showSelectedLabels: true,
        showUnselectedLabels: true,
        type: BottomNavigationBarType.fixed,
        elevation: 12,
        selectedLabelStyle: GoogleFonts.inter(
          fontSize: 11,
          fontWeight: FontWeight.w600,
        ),
        unselectedLabelStyle: GoogleFonts.inter(
          fontSize: 11,
        ),
      ),
    );
  }
}
