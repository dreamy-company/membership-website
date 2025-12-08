<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TreeView extends Component
{
    public $members = [];

    public function mount()
    {
        // contoh data
        $this->members = [
            [
                'id' => 'INF26793251',
                'name' => 'William Walker',
                'level' => 1,
                'children' => [
                    [
                        'id' => 'INF26793639',
                        'name' => 'Bryan Frey',
                        'level' => 2,
                        'children' => []
                    ],
                    [
                        'id' => 'INF26808778',
                        'name' => 'Daniel Wolfe',
                        'level' => 2,
                        'children' => []
                    ],
                    [
                        'id' => 'TROYMARLINDA',
                        'name' => 'Troy Marlinda',
                        'level' => 2,
                        'children' => []
                    ]
                ]
            ],
            [
                'id' => 'INF26821877',
                'name' => 'Dr. Brenda Mcguire',
                'level' => 1,
                'children' => []
            ],
            [
                'id' => 'INF26828361',
                'name' => 'Barbara Frey',
                'level' => 1,
                'children' => []
            ]
        ];
    }

    public function render()
    {
        return view('livewire.tree-view', [
            'members' => $this->members,
        ]);
    }
}
