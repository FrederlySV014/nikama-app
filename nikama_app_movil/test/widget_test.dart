import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:nikama_app_movil/main.dart';

void main() {
  testWidgets('Nikama app smoke test', (WidgetTester tester) async {
    // Build our app and trigger a frame.
    await tester.pumpWidget(const NikamaApp());
    // Verifica que la pantalla splash se muestre
    expect(find.byType(MaterialApp), findsOneWidget);
  });
}
