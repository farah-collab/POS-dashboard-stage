<?php

namespace App\Http\Controllers;

use  App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    //lister toutes les ventes : premiere partie 
    public function index()
    {
        $sales=Sale::with(['user', 'saleItems.product'])->get();
        return response()->json([
            'sales' => $sales
        ],200); 
    }






    //voir une vente par son id
    public function show($id)
    {
        $sale = Sale::with(['user', 'saleItems.product'])->find($id);
        if (!$sale) {
            return response()->json([
                'message' => 'Sale not found'
            ], 404);
        }
        return response()->json([
            'sale' => $sale
        ], 200);
    }
    //créer une vente
     public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,mobile',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        
        try {
            $totalAmount = 0;
            $items = [];
            
            // Calculer le total et préparer les items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Vérifier le stock
                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'error' => 'Insufficient stock for ' . $product->name
                    ], 400);
                }
                
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;
                
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Créer la vente
            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
            ]);
            
            // Créer les sale items et mettre à jour le stock
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
                
                // Réduire le stock
                $item['product']->decrement('stock_quantity', $item['quantity']);
            }
            
            DB::commit();
            
            // Recharger la vente avec les relations
            $sale->load(['user', 'saleItems.product']);
            
            return response()->json([
                'message' => 'Sale created successfully',
                'sale' => $sale
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'error' => 'Failed to create sale',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    //statistiques du dashboard
    public function stats()
    {
        $totalSales = Sale::sum('total_amount');
        $totalTransactions = Sale::count();
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $todayTransactions = Sale::whereDate('created_at', today())->count();
        
        // Top 5 produits vendus
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        // Ventes par jour (7 derniers jours)
        $salesByDay = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        return response()->json([
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'today_sales' => $todaySales,
            'today_transactions' => $todayTransactions,
            'top_products' => $topProducts,
            'sales_by_day' => $salesByDay,
        ], 200);
    }
}

