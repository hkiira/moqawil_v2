class OrderModel {
  final int id;
  final String code;
  final String date;
  final double total;
  final int status;
  final int itemCount;
  final String? firstProductImage;
  final String? firstProductTitle;

  OrderModel({
    required this.id,
    required this.code,
    required this.date,
    required this.total,
    required this.status,
    required this.itemCount,
    this.firstProductImage,
    this.firstProductTitle,
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    int count = 0;
    String? img;
    String? title;

    if (json['orderpacks'] != null) {
      final packs = json['orderpacks'] as List;
      for (var pack in packs) {
        count += (pack['quantity'] as num).toInt();
        if (img == null && pack['pack'] != null && pack['pack']['product'] != null) {
          img = pack['pack']['product']['image'];
          title = pack['pack']['product']['title'];
        }
      }
    }

    return OrderModel(
      id: json['id'],
      code: json['code'] ?? '',
      date: json['created'] ?? '',
      total: (json['total'] as num?)?.toDouble() ?? 0.0,
      status: json['statut'] ?? 0,
      itemCount: count,
      firstProductImage: img != null
          ? (img.startsWith('http') ? img : 'http://localhost/moqa' + img)
          : null,
      firstProductTitle: title,
    );
  }
}
