import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:shared_preferences/shared_preferences.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final TextEditingController _searchController = TextEditingController();
  List<String> _recentSearches = [];

  @override
  void initState() {
    super.initState();
    _loadHistory();
  }

  Future<void> _loadHistory() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _recentSearches = prefs.getStringList('recent_searches') ?? [];
    });
  }

  Future<void> _saveSearch(String query) async {
    if (query.trim().isEmpty) return;
    final prefs = await SharedPreferences.getInstance();
    _recentSearches.remove(query);
    _recentSearches.insert(0, query);
    if (_recentSearches.length > 5) {
      _recentSearches.removeLast();
    }
    await prefs.setStringList('recent_searches', _recentSearches);
  }

  void _submitSearch(String query) {
    if (query.trim().isEmpty) return;
    _saveSearch(query);
    context.push('/catalog', extra: {'search': query});
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: Theme.of(context).colorScheme.primary,
        elevation: 0,
        titleSpacing: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        title: Container(
          margin: const EdgeInsets.only(right: 16),
          height: 40,
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.grey.withOpacity(0.2)),
          ),
          child: TextField(
            controller: _searchController,
            autofocus: true,
            style: const TextStyle(color: Colors.black87),
            textInputAction: TextInputAction.search,
            onSubmitted: _submitSearch,
            decoration: InputDecoration(
              hintText: 'Search products...',
              hintStyle: const TextStyle(color: Colors.black54),
              prefixIcon: const Icon(Icons.search, color: Colors.black54),
              suffixIcon: IconButton(
                icon: const Icon(Icons.close, color: Colors.black54, size: 18),
                onPressed: () {
                  _searchController.clear();
                  setState(() {});
                },
              ),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(vertical: 10),
            ),
          ),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Recent Searches', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          ..._recentSearches.map((search) => ListTile(
            leading: Icon(Icons.history, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
            title: Text(search, style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.8))),
            onTap: () => _submitSearch(search),
          )),
        ],
      ),
    );
  }
}
