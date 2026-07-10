<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\CategoryTreeService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service,
        private readonly CategoryTreeService $tree,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);
        $sort = in_array($request->sort, ['title', 'code', 'weight', 'availability', 'created_at'], true) ? $request->sort : 'created_at';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';
        $products = Product::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title', 'like', '%'.$request->search.'%')->orWhere('code', 'like', '%'.$request->search.'%')))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('availability'), fn ($q) => $q->where('availability', $request->availability))
            ->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $categoryOptions = $this->tree->options();

        return view('products.index', compact('products', 'categoryOptions', 'sort', 'direction'));
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);
        $categoryOptions = $this->tree->options();

        return view('products.create', compact('categoryOptions'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->service->create($request->validated(), $request->file('main_image'), $request->file('gallery', []));

        return redirect()->route('products.show', $product)->with('success', 'محصول با کد '.$product->code.' ایجاد شد.');
    }

    public function show(Product $product): View
    {
        Gate::authorize('view', $product);
        $product->load(['category', 'images']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);
        $product->load('images');
        $categoryOptions = $this->tree->options();

        return view('products.edit', compact('product', 'categoryOptions'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->service->update($product, $request->validated(), $request->file('main_image'), $request->file('gallery', []));

        return redirect()->route('products.show', $product)->with('success', 'محصول به‌روزرسانی شد.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);
        $this->service->delete($product);

        return redirect()->route('products.index')->with('success', 'محصول حذف شد.');
    }
}
