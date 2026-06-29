class LoyaltyPoint {
  final int id;
  final String code;
  final double points;
  final int statut;
  final double valeur;

  LoyaltyPoint({
    required this.id,
    required this.code,
    required this.points,
    required this.statut,
    required this.valeur,
  });

  factory LoyaltyPoint.fromJson(Map<String, dynamic> json) {
    return LoyaltyPoint(
      id: json['id'] ?? 0,
      code: json['code'] ?? '',
      points: (json['points'] ?? 0).toDouble(),
      statut: json['statut'] ?? 0,
      valeur: (json['valeur'] ?? 0).toDouble(),
    );
  }
}

class LoyaltyResponse {
  final double totalPoints;
  final List<LoyaltyPoint> history;

  LoyaltyResponse({
    required this.totalPoints,
    required this.history,
  });

  factory LoyaltyResponse.fromJson(Map<String, dynamic> json) {
    final list = json['history'] as List? ?? [];
    return LoyaltyResponse(
      totalPoints: (json['total_points'] ?? 0).toDouble(),
      history: list.map((item) => LoyaltyPoint.fromJson(item)).toList(),
    );
  }
}
