<?php
/**
 * Live Radio Page
 */

session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$settings = getSettings();
$categories = getCategories();
$breaking_news = getBreakingNews();
$live_program = getCurrentLiveProgram();
$programs = getRadioPrograms(10);

// Log listener
logListener($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? '');

$page_title = 'Live Radio';
include 'includes/header.php';
?>

<div class="container my-5">
    <!-- Main Radio Player -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="radio-player text-center" style="background: linear-gradient(135deg, #c0392b, #e74c3c, #f39c12);">
                <h2 class="mb-3"><i class="fas fa-broadcast-tower"></i> লাইভ রেডিও স্ট্রিম</h2>
                
                <?php if ($live_program): ?>
                <div class="mb-3">
                    <span class="live-indicator fs-5">LIVE NOW</span>
                </div>
                <h3 class="mb-2"><?php echo $live_program['title']; ?></h3>
                <p class="mb-3"><i class="fas fa-user-microphone"></i> <?php echo $live_program['rj_name']; ?></p>
                <?php else: ?>
                <div class="alert alert-warning d-inline-block mb-3">
                    <i class="fas fa-info-circle"></i> Currently offline - Check schedule below
                </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <audio id="mainPlayer" controls preload="none" style="width: 100%; max-width: 500px;">
                        <source src="<?php echo $settings['stream_url']; ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <button class="btn btn-light btn-lg" onclick="adjustVolume(-0.1)">
                        <i class="fas fa-volume-down"></i>
                    </button>
                    <button class="btn btn-light btn-lg" onclick="togglePlay()">
                        <i class="fas fa-play" id="playIcon"></i>
                    </button>
                    <button class="btn btn-light btn-lg" onclick="adjustVolume(0.1)">
                        <i class="fas fa-volume-up"></i>
                    </button>
                </div>
                
                <div class="mt-4">
                    <small class="text-white-50">
                        <i class="fas fa-headphones"></i> 
                        <span id="listenerCount">0</span> listeners online
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Request Song / Chat Section -->
    <div class="row mb-5">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> লাইভ চ্যাট</h5>
                </div>
                <div class="card-body" style="height: 300px; overflow-y: auto;" id="chatBox">
                    <div class="text-muted text-center py-4">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>Chat feature coming soon!</p>
                    </div>
                </div>
                <div class="card-footer">
                    <form id="chatForm" onsubmit="sendChat(event)">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Write a message..." id="chatInput">
                            <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-music"></i> গান রিকোয়েস্ট করুন</h5>
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        <div class="mb-3">
                            <label class="form-label">আপনার নাম</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">গানের নাম</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">বার্তা (ঐচ্ছিক)</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> পাঠান
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Program Schedule -->
    <div class="row">
        <div class="col-12">
            <h2 class="widget-title mb-4">আজকের অনুষ্ঠানসূচি</h2>
            <div class="table-responsive">
                <table class="table table-hover bg-white rounded shadow-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>সময়</th>
                            <th>অনুষ্ঠান</th>
                            <th>বর্ণনা</th>
                            <th>উপস্থাপনায়</th>
                            <th>স্ট্যাটাস</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($programs as $prog): 
                        $is_current = ($live_program && $live_program['id'] == $prog['id']);
                        ?>
                        <tr class="<?php echo $is_current ? 'table-success' : ''; ?>">
                            <td>
                                <strong><?php echo date('g:i A', strtotime($prog['start_time'])); ?></strong> - 
                                <?php echo date('g:i A', strtotime($prog['end_time'])); ?>
                            </td>
                            <td><strong><?php echo $prog['title']; ?></strong></td>
                            <td><?php echo $prog['description'] ?? '-'; ?></td>
                            <td><i class="fas fa-user-microphone"></i> <?php echo $prog['rj_name']; ?></td>
                            <td>
                                <?php if ($is_current): ?>
                                <span class="badge bg-success pulse">LIVE</span>
                                <?php elseif (strtotime($prog['start_time']) > time()): ?>
                                <span class="badge bg-secondary">Upcoming</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Ended</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const player = document.getElementById('mainPlayer');
const playIcon = document.getElementById('playIcon');

function togglePlay() {
    if (player.paused) {
        player.play();
        playIcon.classList.remove('fa-play');
        playIcon.classList.add('fa-pause');
    } else {
        player.pause();
        playIcon.classList.remove('fa-pause');
        playIcon.classList.add('fa-play');
    }
}

function adjustVolume(change) {
    player.volume = Math.max(0, Math.min(1, player.volume + change));
}

function sendChat(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    if (input.value.trim()) {
        // AJAX implementation for chat would go here
        alert('Chat feature will be implemented with WebSocket!');
        input.value = '';
    }
}

// Simulate listener count (replace with real-time data)
setInterval(() => {
    const count = Math.floor(Math.random() * 50) + 10;
    document.getElementById('listenerCount').textContent = count;
}, 5000);

// Auto-update play icon
player.addEventListener('play', () => {
    playIcon.classList.remove('fa-play');
    playIcon.classList.add('fa-pause');
});

player.addEventListener('pause', () => {
    playIcon.classList.remove('fa-pause');
    playIcon.classList.add('fa-play');
});
</script>

<style>
.pulse {
    animation: pulse-animation 2s infinite;
}
@keyframes pulse-animation {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<?php include 'includes/footer.php'; ?>
