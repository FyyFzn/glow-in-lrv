@php
    $leaders = [
        ['name' => 'LunaStar', 'points' => '1250 pts', 'img' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80'],
        ['name' => 'PixelForge', 'points' => '1180 pts', 'img' => 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=100&q=80'],
        ['name' => 'CodeWhisper', 'points' => '1020 pts', 'img' => 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&w=100&q=80'],
        ['name' => 'DesignFlow', 'points' => '950 pts', 'img' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=100&q=80'],
        ['name' => 'DataPilot', 'points' => '880 pts', 'img' => 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?auto=format&fit=crop&w=100&q=80'],
    ];
@endphp

<aside class="sidebar-right">
    <div class="search-bar-desktop">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search Glow-in">
    </div>
    <div class="leaderboard-card">
        <h3>Leaderboard</h3>
        <ul class="leaderboard-list">
            @foreach ($leaders as $leader)
                <li class="leaderboard-item">
                    <img src="{{ $leader['img'] }}" alt="{{ $leader['name'] }}">
                    <span class="name">{{ $leader['name'] }}</span>
                    <span class="points">{{ $leader['points'] }}</span>
                </li>
            @endforeach
        </ul>
        <a href="#" class="view-full">View Full Leaderboard</a>
    </div>
</aside>
