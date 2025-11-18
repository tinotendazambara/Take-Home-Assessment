class Product {
  final int id;
  final String name;
  final String? description;
  final double price;
  final int stock;
  final String? category;

  Product({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    required this.stock,
    this.category,
  });

  // BUG: No validation - price could be negative or null
  // BUG: No null safety check for required fields
  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      price: json['price']?.toDouble() ?? 0.0, // BUG: Defaults to 0 if null
      stock: json['stock'] ?? 0, // BUG: Defaults to 0 if null
      category: json['category'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'price': price,
      'stock': stock,
      'category': category,
    };
  }

  // BUG: No validation - could return true for negative stock
  bool get isInStock => stock > 0;
}

