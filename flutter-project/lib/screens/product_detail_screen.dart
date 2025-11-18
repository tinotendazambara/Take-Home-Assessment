import 'package:flutter/material.dart';
import '../models/product.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class ProductDetailScreen extends StatefulWidget {
  final Product product;
  final User user;

  const ProductDetailScreen({
    super.key,
    required this.product,
    required this.user,
  });

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  final _apiService = ApiService();
  Product? _product;
  bool _isLoading = false;

  // BUG: Fetches product again even though it's already passed in
  // BUG: No caching - refetches every time

  @override
  void initState() {
    super.initState();
    _product = widget.product;
    _loadProductDetails();
  }

  Future<void> _loadProductDetails() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final product = await _apiService.getProduct(widget.product.id);
      setState(() {
        _product = product;
        _isLoading = false;
      });
    } catch (e) {
      // BUG: Error handling - doesn't show error to user
      // BUG: Still shows old product data even if fetch fails
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _addToCart() async {
    // BUG: No cart functionality implemented
    // BUG: No feedback to user
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Feature not implemented')),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    // BUG: No null check - will crash if _product is null
    final product = _product!;

    return Scaffold(
      appBar: AppBar(
        title: Text(product.name),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // BUG: No image display - product might have images
            Text(
              product.name,
              style: Theme.of(context).textTheme.headlineMedium,
            ),
            const SizedBox(height: 8),
            Text(
              '\$${product.price.toStringAsFixed(2)}',
              style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                    color: Colors.green,
                  ),
            ),
            const SizedBox(height: 16),
            // BUG: No stock display
            // BUG: No category display
            if (product.description != null) ...[
              Text(
                'Description',
                style: Theme.of(context).textTheme.titleMedium,
              ),
              const SizedBox(height: 8),
              Text(product.description!),
              const SizedBox(height: 16),
            ],
            // BUG: Button enabled even if product is out of stock
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: product.isInStock ? _addToCart : null,
                child: Text(product.isInStock ? 'Add to Cart' : 'Out of Stock'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

