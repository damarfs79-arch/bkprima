import 'dart:async';
import 'package:flutter/material.dart';
import '../core/theme/app_theme.dart';
import '../data/rules_data.dart';

class RuleLookupSheet extends StatefulWidget {
  final ValueChanged<RuleItem>? onRuleSelected;

  const RuleLookupSheet({
    super.key,
    this.onRuleSelected,
  });

  static Future<RuleItem?> show(BuildContext context) {
    return showModalBottomSheet<RuleItem>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => const RuleLookupSheet(),
    );
  }

  @override
  State<RuleLookupSheet> createState() => _RuleLookupSheetState();
}

class _RuleLookupSheetState extends State<RuleLookupSheet> {
  final _searchController = TextEditingController();
  Timer? _debounce;
  List<RuleItem> _filteredRules = RulesData.items;

  @override
  void dispose() {
    _debounce?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  void _onSearchChanged(String query) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 300), () {
      final q = query.trim().toLowerCase();
      setState(() {
        if (q.isEmpty) {
          _filteredRules = RulesData.items;
        } else {
          _filteredRules = RulesData.items.where((rule) {
            return rule.code.toLowerCase().contains(q) ||
                rule.category.toLowerCase().contains(q) ||
                rule.description.toLowerCase().contains(q) ||
                rule.defaultPenalty.toLowerCase().contains(q);
          }).toList();
        }
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    final bottomInset = MediaQuery.of(context).viewInsets.bottom;
    final maxSheetHeight = MediaQuery.of(context).size.height * 0.8;

    return Container(
      constraints: BoxConstraints(maxHeight: maxSheetHeight),
      margin: EdgeInsets.only(bottom: bottomInset),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
        border: Border.all(color: AppTheme.border, width: 1),
      ),
      child: SafeArea(
        top: false,
        child: Column(
          children: [
            // Drag Handle
            const SizedBox(height: 10),
            Center(
              child: Container(
                width: 36,
                height: 4,
                decoration: BoxDecoration(
                  color: AppTheme.borderDark,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 12),

            // Header
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Buku Saku Tata Tertib & Poin',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.textPrimary,
                            letterSpacing: -0.3,
                          ),
                        ),
                        Text(
                          'Pedoman skor pembinaan kedisiplinan SMK PGRI 5 Jember',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, size: 20, color: AppTheme.textSecondary),
                    visualDensity: VisualDensity.compact,
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 10),

            // Search input
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: TextField(
                controller: _searchController,
                onChanged: _onSearchChanged,
                style: const TextStyle(
                  fontFamily: AppTheme.fontFamily,
                  fontSize: 13,
                  fontWeight: FontWeight.w500,
                  color: AppTheme.textPrimary,
                ),
                decoration: InputDecoration(
                  filled: true,
                  fillColor: AppTheme.background,
                  hintText: 'Cari aturan, kata kunci, atau sanksi...',
                  hintStyle: const TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 13,
                    color: AppTheme.textMuted,
                  ),
                  prefixIcon: const Icon(Icons.search, size: 18, color: AppTheme.textSecondary),
                  suffixIcon: _searchController.text.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear, size: 16, color: AppTheme.textSecondary),
                          onPressed: () {
                            _searchController.clear();
                            _onSearchChanged('');
                          },
                        )
                      : null,
                  contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: const BorderSide(color: AppTheme.border, width: 1),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: const BorderSide(color: AppTheme.border, width: 1),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: const BorderSide(color: AppTheme.primaryFocus, width: 1.5),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 10),
            const Divider(color: AppTheme.border, height: 1),

            // List of rules
            Expanded(
              child: _filteredRules.isEmpty
                  ? Container(
                      alignment: Alignment.center,
                      padding: const EdgeInsets.all(24),
                      child: const Text(
                        'Aturan tidak ditemukan',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 13,
                          color: AppTheme.textSecondary,
                        ),
                      ),
                    )
                  : ListView.separated(
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      itemCount: _filteredRules.length,
                      separatorBuilder: (context, index) => const Divider(
                        color: AppTheme.border,
                        height: 1,
                        indent: 16,
                        endIndent: 16,
                      ),
                      itemBuilder: (context, index) {
                        final rule = _filteredRules[index];
                        final isHighRisk = rule.points >= 25;

                        return InkWell(
                          onTap: () {
                            Navigator.of(context).pop(rule);
                          },
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Point Badge
                                Container(
                                  width: 44,
                                  height: 44,
                                  decoration: BoxDecoration(
                                    color: isHighRisk ? AppTheme.dangerLight : AppTheme.primaryLight,
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(
                                      color: isHighRisk ? AppTheme.dangerBorder : AppTheme.primaryBorder,
                                      width: 1,
                                    ),
                                  ),
                                  alignment: Alignment.center,
                                  child: Text(
                                    '+${rule.points}',
                                    style: TextStyle(
                                      fontFamily: AppTheme.fontFamily,
                                      fontSize: 13,
                                      fontWeight: FontWeight.w700,
                                      color: isHighRisk ? AppTheme.danger : AppTheme.primary,
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                // Description & Category
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Container(
                                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                            decoration: BoxDecoration(
                                              color: AppTheme.surfaceMuted,
                                              borderRadius: BorderRadius.circular(4),
                                              border: Border.all(color: AppTheme.border, width: 1),
                                            ),
                                            child: Text(
                                              rule.code,
                                              style: const TextStyle(
                                                fontFamily: AppTheme.fontFamily,
                                                fontSize: 9,
                                                fontWeight: FontWeight.w700,
                                                color: AppTheme.textSecondary,
                                              ),
                                            ),
                                          ),
                                          const SizedBox(width: 6),
                                          Text(
                                            rule.category,
                                            style: const TextStyle(
                                              fontFamily: AppTheme.fontFamily,
                                              fontSize: 11,
                                              fontWeight: FontWeight.w500,
                                              color: AppTheme.textMuted,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        rule.description,
                                        style: const TextStyle(
                                          fontFamily: AppTheme.fontFamily,
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600,
                                          color: AppTheme.textPrimary,
                                        ),
                                      ),
                                      const SizedBox(height: 3),
                                      Text(
                                        'Tindakan: ${rule.defaultPenalty}',
                                        style: const TextStyle(
                                          fontFamily: AppTheme.fontFamily,
                                          fontSize: 11,
                                          color: AppTheme.textSecondary,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const Icon(Icons.chevron_right, size: 18, color: AppTheme.textMuted),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }
}
