
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== DIAGNOSTIC DES SESSIONS ===\n\n";

// 1. Vérifier la structure de la table sessions
echo "1. Structure de la table sessions :\n";
$columns = DB::select("DESCRIBE sessions");
foreach ($columns as $column) {
    echo "   - {$column->Field}: {$column->Type}\n";
}

echo "\n2. Contenu actuel des sessions :\n";
$sessions = DB::table('sessions')->whereNotNull('user_id')->get();

if ($sessions->isEmpty()) {
    echo "   ❌ AUCUNE SESSION TROUVÉE AVEC user_id\n";
    echo "   Cela explique pourquoi tous les utilisateurs apparaissent comme 'Jamais connecté'\n\n";

    echo "   Sessions sans user_id :\n";
    $allSessions = DB::table('sessions')->take(5)->get();
    foreach ($allSessions as $session) {
        echo "     - ID: {$session->id}, user_id: " . ($session->user_id ?? 'NULL') . ", last_activity: {$session->last_activity}\n";
    }
} else {
    echo "   ✅ Sessions trouvées :\n";
    foreach ($sessions as $session) {
        $readableTime = Carbon::createFromTimestamp($session->last_activity)->format('d/m/Y H:i:s');
        $diffInSeconds = now()->timestamp - $session->last_activity;
        $status = $diffInSeconds < 300 ? '🟢 EN LIGNE' : '🔴 DÉCONNECTÉ';

        echo "     - User ID: {$session->user_id}\n";
        echo "       Timestamp: {$session->last_activity}\n";
        echo "       Date lisible: {$readableTime}\n";
        echo "       Différence: {$diffInSeconds} secondes\n";
        echo "       Statut: {$status}\n\n";
    }
}

echo "3. Vérification des utilisateurs :\n";
$users = DB::table('users')->select('id', 'name', 'email')->get();
foreach ($users as $user) {
    $hasSession = $sessions->where('user_id', $user->id)->first();
    echo "   - {$user->name} ({$user->email}): " . ($hasSession ? '✅ A une session' : '❌ Pas de session') . "\n";
}

echo "\n4. Configuration des sessions :\n";
echo "   - Driver: " . config('session.driver') . "\n";
echo "   - Lifetime: " . config('session.lifetime') . " minutes\n";
echo "   - Expire on close: " . (config('session.expire_on_close') ? 'Oui' : 'Non') . "\n";

echo "\n5. Test de connexion récente :\n";
$currentTimestamp = now()->timestamp;
echo "   - Timestamp actuel: {$currentTimestamp}\n";
echo "   - Seuil en ligne (5 min): " . ($currentTimestamp - 300) . "\n";

// Suggestions de correction
echo "\n=== SUGGESTIONS DE CORRECTION ===\n";

if ($sessions->isEmpty()) {
    echo "❗ PROBLÈME PRINCIPAL : Aucune session avec user_id trouvée\n";
    echo "💡 SOLUTIONS :\n";
    echo "   1. Vérifiez que les utilisateurs se connectent bien via Auth::login()\n";
    echo "   2. Vérifiez la configuration session.driver dans .env\n";
    echo "   3. Assurez-vous que la middleware 'web' est appliquée\n";
    echo "   4. Vérifiez que SESSION_DRIVER=database dans .env\n";
}

if (!$sessions->isEmpty()) {
    $recentSessions = $sessions->filter(function ($session) {
        return (now()->timestamp - $session->last_activity) < 300;
    });

    if ($recentSessions->isEmpty()) {
        echo "❗ PROBLÈME : Aucune session récente (< 5 min)\n";
        echo "💡 Les utilisateurs connectés il y a plus de 5 min apparaissent comme déconnectés\n";
    } else {
        echo "✅ Sessions récentes trouvées : " . $recentSessions->count() . "\n";
    }
}