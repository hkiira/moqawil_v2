class ProfileModel {
  final int id;
  final String name;
  final String phone;
  final String adresse;
  final double walletBalance;

  ProfileModel({
    required this.id,
    required this.name,
    required this.phone,
    required this.adresse,
    required this.walletBalance,
  });

  factory ProfileModel.fromJson(Map<String, dynamic> json) {
    return ProfileModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      phone: json['phone'] ?? '',
      adresse: json['adresse'] ?? '',
      walletBalance: (json['wallet_balance'] as num?)?.toDouble() ?? 0.0,
    );
  }
}
