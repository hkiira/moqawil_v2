import 'dart:ui';
import 'package:flutter/material.dart';

class NotificationsScreen extends StatelessWidget {
  const NotificationsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final List<Map<String, String>> notifications = [
      {
        "title": "Welcome to Moqa B2B!",
        "subtitle": "Thanks for joining us. Check out the latest products in our catalog.",
        "time": "Just now",
        "icon": "welcome"
      },
      {
        "title": "Flash Sale: 20% Off Electronics",
        "subtitle": "Use code FLASH20 at checkout for massive savings on wholesale electronics.",
        "time": "2 hours ago",
        "icon": "sale"
      },
      {
        "title": "Order #1024 Shipped",
        "subtitle": "Your recent order has been shipped and is on its way to you.",
        "time": "1 day ago",
        "icon": "shipping"
      }
    ];

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              children: [
                Row(
                  children: [
                    IconButton(icon: Icon(Icons.arrow_back, color: Theme.of(context).colorScheme.onSurface), onPressed: () => Navigator.pop(context)),
                    Text('Notifications', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 24, fontWeight: FontWeight.bold)),
                  ],
                ),
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.all(24),
                    itemCount: notifications.length,
                    itemBuilder: (context, index) {
                      final notif = notifications[index];
                      return _buildNotificationCard(context, notif['title']!, notif['subtitle']!, notif['time']!, notif['icon']!);
                    },
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNotificationCard(BuildContext context, String title, String subtitle, String time, String iconType) {
    IconData icon;
    Color iconColor;

    switch (iconType) {
      case 'welcome':
        icon = Icons.waving_hand;
        iconColor = Colors.amber.shade700;
        break;
      case 'sale':
        icon = Icons.local_offer;
        iconColor = Colors.green.shade700;
        break;
      case 'shipping':
        icon = Icons.local_shipping;
        iconColor = Colors.blue.shade700;
        break;
      default:
        icon = Icons.notifications;
        iconColor = Theme.of(context).colorScheme.primary;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withOpacity(0.15)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: iconColor.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: iconColor),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          title,
                          style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 16, fontWeight: FontWeight.bold),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      Text(
                        time,
                        style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.4), fontSize: 12),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    subtitle,
                    style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7), fontSize: 14, height: 1.4),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
