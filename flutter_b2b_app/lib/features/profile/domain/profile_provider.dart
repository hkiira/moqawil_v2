import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/profile_repository.dart';
import 'profile_model.dart';

final profileProvider = FutureProvider.autoDispose<ProfileModel?>((ref) async {
  return ref.watch(profileRepositoryProvider).fetchProfile();
});
