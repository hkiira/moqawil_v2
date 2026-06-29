import 'package:go_router/go_router.dart';
import '../../features/init/splash_screen.dart';
import '../../features/auth/presentation/login_screen.dart';
import '../../features/auth/presentation/signup_screen.dart';
import '../../features/main/presentation/main_screen.dart';
import '../../features/catalog/presentation/search_screen.dart';
import '../../features/catalog/presentation/catalog_screen.dart';
import '../../features/catalog/presentation/product_details_screen.dart';
import '../../features/wishlist/presentation/wishlist_screen.dart';
import '../../features/profile/presentation/loyalty_screen.dart';
import '../../features/profile/presentation/account_info_screen.dart';
import '../../features/profile/presentation/notifications_screen.dart';
import '../../features/init/onboarding_screen.dart';

final goRouter = GoRouter(
  initialLocation: '/',
  routes: [
    GoRoute(path: '/', builder: (context, state) => const SplashScreen()),
    GoRoute(path: '/onboarding', builder: (context, state) => const OnboardingScreen()),
    GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
    GoRoute(path: '/signup', builder: (context, state) => const SignupScreen()),
    GoRoute(path: '/home', builder: (context, state) => const MainScreen()),
    GoRoute(path: '/search', builder: (context, state) => const SearchScreen()),
    GoRoute(path: '/wishlist', builder: (context, state) => const WishlistScreen()),
    GoRoute(path: '/loyalty', builder: (context, state) => const LoyaltyScreen()),
    GoRoute(path: '/account_info', builder: (context, state) => const AccountInfoScreen()),
    GoRoute(path: '/notifications', builder: (context, state) => const NotificationsScreen()),
    GoRoute(
      path: '/catalog',
      builder: (context, state) {
        final extra = state.extra as Map<String, dynamic>?;
        return CatalogScreen(
          searchQuery: extra?['search'],
          categoryId: extra?['categoryId'],
        );
      },
    ),
    GoRoute(
      path: '/product_details',
      builder: (context, state) {
        final product = state.extra as Map<String, dynamic>;
        return ProductDetailsScreen(product: product);
      },
    ),
  ],
);
