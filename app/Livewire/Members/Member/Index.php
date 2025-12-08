<?php

namespace App\Livewire\Members\Member;

use Livewire\Component;
use App\Models\Member;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $tree = [];
    public $title = "Member";
    
    public function mount()
    {
        $this->loadRoot();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->loadRoot();
    }

    private function loadRoot()
    {
        $roots = Member::search($this->search)
            ->where('parent_member_id', auth()->user()->id)
            ->where('user_id', '!=', auth()->user()->id)
            ->get();

        $this->tree = $roots->map(fn($m) => $this->formatNode($m, 1))->toArray();

    }

    private function formatNode($m, $level = 1)
    {
        return [
            'id' => $m->id,
            'user_id' => $m->user_id,
            'member_code' => $m->member_code,
            'phone_number' => $m->phone_number,
            'user' => [
                'name' => $m->user->name ?? 'Tanpa Nama',
                'profile_picture' => $m->user->profile_picture ?? null,
            ],
            'children' => [],
            'expanded' => false,
            'loading' => false,
            'fetched' => false,
            'level' => $level,
        ];
    }

    public function toggleNode($memberId)
    {
        $this->updateNode($this->tree, $memberId, function (&$node) {

            // Stop jika mencapai level 5
            if ($node['level'] >= 5) {
                // bisa kasih notifikasi, bisa juga diam saja
                return;
            }

            if (!$node['fetched']) {
                $node['loading'] = true;

                $children = Member::where('parent_member_id', $node['id'])
                    ->with('user')
                    ->get();

                $node['children'] = $children->map(
                    fn($m) =>
                    $this->formatNode($m, $node['level'] + 1)
                )->toArray();

                $node['fetched'] = true;
                $node['loading'] = false;
            }

            $node['expanded'] = !$node['expanded'];
        });
    }


    private function updateNode(&$nodes, $id, $callback)
    {
        foreach ($nodes as &$node) {

            if ($node['id'] == $id) {
                $callback($node);
                return true;
            }

            if (!empty($node['children'])) {
                if ($this->updateNode($node['children'], $id, $callback)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function render()
    {
        
        $totalMembers = Member::where('parent_member_id', auth()->user()->id)->count();
        return view('livewire.members.member.index', [
            'members' => $this->tree,
        ]);
    }
}
