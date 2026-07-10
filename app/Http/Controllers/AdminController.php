<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private readonly AdminService $service) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Admin::class);
        $sort = in_array($request->sort, ['name', 'mobile', 'is_active', 'created_at'], true) ? $request->sort : 'created_at';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';
        $admins = Admin::query()
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', '%'.$request->search.'%')->orWhere('mobile', 'like', '%'.$request->search.'%')))
            ->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admins.index', compact('admins', 'sort', 'direction'));
    }

    public function create(): View
    {
        Gate::authorize('create', Admin::class);

        return view('admins.create');
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admins.index')->with('success', 'مدیر با موفقیت ایجاد شد.');
    }

    public function edit(Admin $admin): View
    {
        Gate::authorize('update', $admin);

        return view('admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        $this->service->update($admin, $request->validated(), $request->user());

        return redirect()->route('admins.index')->with('success', 'اطلاعات مدیر به‌روزرسانی شد.');
    }

    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        Gate::authorize('delete', $admin);
        $this->service->delete($admin, $request->user());

        return redirect()->route('admins.index')->with('success', 'مدیر حذف شد.');
    }
}
