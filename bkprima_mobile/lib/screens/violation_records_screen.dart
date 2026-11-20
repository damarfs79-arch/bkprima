import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/app_surface.dart';
import '../models/violation_model.dart';
import '../providers/violation_provider.dart';
import '../widgets/stat_card.dart';
import 'input_violation_screen.dart';

class ViolationRecordsScreen extends StatefulWidget {
  const ViolationRecordsScreen({super.key});

  @override
  State<ViolationRecordsScreen> createState() => _ViolationRecordsScreenState();
}

class _ViolationRecordsScreenState extends State<ViolationRecordsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  String _selectedStatusFilter = 'Semua';

  static const List<String> _filters = [
    'Semua',
    'Dalam Pembinaan',
    'Panggilan Ortu',
    'Selesai Dibina',
  ];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ViolationProvider>().fetchViolations();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  List<ViolationRecord> _filterViolations(List<ViolationRecord> list) {
    if (_selectedStatusFilter == 'Semua') return list;
    return list.where((v) {
      return v.status.toLowerCase().contains(_selectedStatusFilter.toLowerCase());
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<ViolationProvider>();
    final stats = provider.stats;

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Catatan Pelanggaran & SP'),
        actions: [
          IconButton(
            tooltip: 'Catat Pelanggaran',
            icon: const Icon(Icons.add_circle_outline_rounded, color: AppTheme.primary, size: 22),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const InputViolationScreen()),
              );
            },
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primary,
          unselectedLabelColor: AppTheme.textSecondary,
          indicatorColor: AppTheme.primary,
          indicatorWeight: 2,
          dividerColor: AppTheme.border,
          labelStyle: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            fontWeight: FontWeight.w700,
          ),
          unselectedLabelStyle: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            fontWeight: FontWeight.w500,
          ),
          tabs: const [
            Tab(text: 'Rekaman Kasus'),
            Tab(text: 'Panggilan Ortu (SP)'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          // Tab 1: Rekaman Kasus
          RefreshIndicator(
            onRefresh: () => provider.fetchViolations(),
            color: AppTheme.primary,
            child: CustomScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              slivers: [
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Summary Grid
                        GridView.count(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          crossAxisCount: 2,
                          crossAxisSpacing: 10,
                          mainAxisSpacing: 10,
                          childAspectRatio: 1.65,
                          children: [
                            StatCard(
                              title: 'Total Pelanggaran',
                              value: '${stats?.totalKasus ?? 0}',
                              subtitle: 'Tercatat semester ini',
                              valueColor: AppTheme.textPrimary,
                            ),
                            StatCard(
                              title: 'Kasus Ringan',
                              value: '${stats?.kasusRingan ?? 0}',
                              subtitle: '+5 / +10 Poin Tatib',
                              valueColor: AppTheme.success,
                            ),
                            StatCard(
                              title: 'Peringatan SP',
                              value: '${stats?.peringatanSp ?? 0}',
                              subtitle: '+15 / +25 Poin Tatib',
                              valueColor: AppTheme.warning,
                            ),
                            StatCard(
                              title: 'Panggilan Ortu',
                              value: '${stats?.panggilanOrtu ?? 0}',
                              subtitle: 'Ambang batas SP tercapai',
                              valueColor: AppTheme.danger,
                            ),
                          ],
                        ),
                        const SizedBox(height: 14),

                        // Filter Chips
                        SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: Row(
                            children: _filters.map((f) {
                              final isSelected = _selectedStatusFilter == f;
                              return Padding(
                                padding: const EdgeInsets.only(right: 6),
                                child: InkWell(
                                  onTap: () {
                                    HapticFeedback.selectionClick();
                                    setState(() {
                                      _selectedStatusFilter = f;
                                    });
                                  },
                                  borderRadius: BorderRadius.circular(8),
                                  child: AnimatedContainer(
                                    duration: const Duration(milliseconds: 150),
                                    curve: Curves.easeOut,
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    decoration: BoxDecoration(
                                      color: isSelected ? AppTheme.primaryLight : AppTheme.surface,
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(
                                        color: isSelected ? AppTheme.primaryFocus : AppTheme.border,
                                        width: isSelected ? 1.5 : 1.0,
                                      ),
                                    ),
                                    child: Text(
                                      f,
                                      style: TextStyle(
                                        fontFamily: AppTheme.fontFamily,
                                        fontSize: 11,
                                        fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                                        color: isSelected ? AppTheme.primary : AppTheme.textSecondary,
                                      ),
                                    ),
                                  ),
                                ),
                              );
                            }).toList(growable: false),
                          ),
                        ),
                        const SizedBox(height: 12),
                      ],
                    ),
                  ),
                ),
                _buildViolationSliverList(provider),
              ],
            ),
          ),

          // Tab 2: Surat Pemanggilan Ortu (SP)
          RefreshIndicator(
            onRefresh: () => provider.fetchViolations(),
            color: AppTheme.primary,
            child: _buildSuratPanggilanList(provider),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const InputViolationScreen()),
          );
        },
        backgroundColor: AppTheme.primary,
        elevation: 2,
        icon: const Icon(Icons.edit_note_rounded, color: Colors.white, size: 20),
        label: const Text(
          'Catat Pelanggaran',
          style: TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            fontWeight: FontWeight.w700,
            color: Colors.white,
          ),
        ),
      ),
    );
  }

  Widget _buildViolationSliverList(ViolationProvider provider) {
    if (provider.isLoading) {
      return const SliverFillRemaining(
        hasScrollBody: false,
        child: Center(
          child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
        ),
      );
    }

    final filtered = _filterViolations(provider.violations);
    if (filtered.isEmpty) {
      return const SliverFillRemaining(
        hasScrollBody: false,
        child: Center(
          child: Text(
            'Tidak ada catatan pelanggaran yang sesuai filter.',
            style: TextStyle(
              fontFamily: AppTheme.fontFamily,
              fontSize: 12,
              color: AppTheme.textSecondary,
            ),
          ),
        ),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.only(left: 16, right: 16, bottom: 20),
      sliver: SliverList.builder(
        itemCount: filtered.length,
        itemBuilder: (context, index) {
          final v = filtered[index];
          final isHighPoints = (v.points ?? 0) >= 20;

          return Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: AppSurface(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        '${v.date} • ${v.time} WIB',
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 10,
                          color: AppTheme.textMuted,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: isHighPoints ? AppTheme.dangerLight : AppTheme.primaryLight,
                          borderRadius: BorderRadius.circular(6),
                          border: Border.all(
                            color: isHighPoints ? AppTheme.dangerBorder : AppTheme.primaryBorder,
                            width: 1,
                          ),
                        ),
                        child: Text(
                          v.points != null ? '+${v.points} Poin Tatib' : 'Tanpa Poin',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 10,
                            fontWeight: FontWeight.w700,
                            color: isHighPoints ? AppTheme.danger : AppTheme.primary,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(
                    v.studentName,
                    style: const TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  Text(
                    '${v.studentClass} • Kategori: ${v.category}',
                    style: const TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 11,
                      color: AppTheme.textSecondary,
                    ),
                  ),
                  const Divider(height: 14, color: AppTheme.border),
                  Text(
                    v.violationName,
                    style: const TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  if (v.notes.isNotEmpty) ...[
                    const SizedBox(height: 4),
                    Text(
                      'Catatan: ${v.notes}',
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        color: AppTheme.textSecondary,
                        height: 1.4,
                      ),
                    ),
                  ],
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Pelapor: ${v.reporter}',
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 10,
                          color: AppTheme.textMuted,
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                        decoration: BoxDecoration(
                          color: AppTheme.surfaceMuted,
                          borderRadius: BorderRadius.circular(4),
                          border: Border.all(color: AppTheme.border, width: 1),
                        ),
                        child: Text(
                          v.status,
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildSuratPanggilanList(ViolationProvider provider) {
    if (provider.isLoading) {
      return const Center(child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary));
    }
    if (provider.suratPanggilan.isEmpty) {
      return const Center(
        child: Text(
          'Tidak ada siswa yang mencapai batas Surat Panggilan (SP).',
          style: TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            color: AppTheme.textSecondary,
          ),
        ),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      itemCount: provider.suratPanggilan.length,
      separatorBuilder: (_, index) => const SizedBox(height: 10),
      itemBuilder: (context, index) {
        final sp = provider.suratPanggilan[index];
        return AppSurface(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppTheme.dangerLight,
                      borderRadius: BorderRadius.circular(6),
                      border: Border.all(color: AppTheme.dangerBorder, width: 1),
                    ),
                    child: Text(
                      sp.spLevel,
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: AppTheme.danger,
                      ),
                    ),
                  ),
                  Text(
                    'Akumulasi: ${sp.totalPoints} Poin Tatib',
                    style: const TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.danger,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Text(
                sp.name,
                style: const TextStyle(
                  fontFamily: AppTheme.fontFamily,
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: AppTheme.textPrimary,
                ),
              ),
              Text(
                'NISN: ${sp.nisn} • ${sp.className}',
                style: const TextStyle(
                  fontFamily: AppTheme.fontFamily,
                  fontSize: 11,
                  color: AppTheme.textSecondary,
                ),
              ),
              const Divider(height: 14, color: AppTheme.border),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      'Wali: ${sp.parentName} (${sp.parentPhone})',
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        color: AppTheme.textSecondary,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 8),
                  InkWell(
                    onTap: () {
                      HapticFeedback.selectionClick();
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Surat Panggilan Orang Tua untuk ${sp.name} siap dicetak.'),
                          backgroundColor: AppTheme.textPrimary,
                        ),
                      );
                    },
                    borderRadius: BorderRadius.circular(6),
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        color: AppTheme.danger,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: const Text(
                        'Cetak SP',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }
}
