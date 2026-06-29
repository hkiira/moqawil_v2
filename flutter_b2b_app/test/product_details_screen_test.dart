import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:b2b_app/features/catalog/presentation/product_details_screen.dart';

void main() {
  group('ProductDetailsScreen Tests', () {
    testWidgets('displays bonus rule banner when configured', (WidgetTester tester) async {
      final product = {
        'id': 42,
        'title': 'Test Product',
        'price': 100.0,
        'image': '',
        'category': 'Seeds',
        'description': 'A very nice test product.',
        'bonusAmount': 5.0,
        'bonusUnitThreshold': 10.0,
        'measurementUnitAbbreviation': 'Kg',
      };

      await tester.pumpWidget(
        MaterialApp(
          home: ProviderScope(
            child: ProductDetailsScreen(product: product),
          ),
        ),
      );

      // Verify that the bonus text is displayed
      expect(find.text('Earn 5 DH per 10 Kg'), findsOneWidget);
      expect(find.byIcon(Icons.stars), findsOneWidget);
    });

    testWidgets('does not display bonus rule banner when not configured', (WidgetTester tester) async {
      final product = {
        'id': 43,
        'title': 'Another Product',
        'price': 50.0,
        'image': '',
        'category': 'Fertilizers',
        'description': 'Another nice product.',
      };

      await tester.pumpWidget(
        MaterialApp(
          home: ProviderScope(
            child: ProductDetailsScreen(product: product),
          ),
        ),
      );

      // Verify that the bonus text is NOT displayed
      expect(find.textContaining('Earn'), findsNothing);
      expect(find.byIcon(Icons.stars), findsNothing);
    });
  });
}
