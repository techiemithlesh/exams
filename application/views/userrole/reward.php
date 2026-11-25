<style>
    .nav-tab {
        padding: 10px 20px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }

    .nav-tab.tab-active {
        border-bottom: 2px solid #f46c6c;
        font-weight: bold;
        color: #f46c6c;
    }


    /* GRID */
    .reward-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    /* CARD */
    .reward-card {
        border-radius: 14px;
        transition: all 0.25s ease;
    }

    .reward-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
    }

    /* HEADER */
    .reward-title {
        font-weight: 600;
        font-size: 15px;
    }

    .reward-sub {
        font-size: 12px;
        color: #888;
    }

    /* RULE ROW */
    .reward-rule {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13px;
    }

    /* COIN BADGE */
    .coin-badge {
        background: #f39c12;
        color: #fff;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }

    /* SCOPE BADGE */
    .scope-badge {
        background: #5bc0de;
        color: #fff;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
    }

    /* WALLET */
    .wallet-badge {
        background: linear-gradient(145deg, #ff735c, #ff8f7a);
        color: #fff;
        padding: 12px 30px;
        border-radius: 22px;
        font-size: 26px;
        font-weight: bold;
    }


    .tab-content {
        margin-top: 20px;
    }
</style>

<section class="panel">
    <header class="panel-heading text-center">
        <h4 class="panel-title"><i class="fas fa-coins"></i> <?= translate('my_coins') ?></h4>
    </header>

    <div class="panel-body text-center">
        <p class="mb-1"><?= translate('your_available_reward_coins') ?>:</p>
        <div
            style="display:inline-block; padding:10px 25px; background: linear-gradient(145deg, #ff735c, #ff8f7a); color:white; border-radius:20px; font-size:24px; font-weight:bold;">
            <?= isset($wallet['total_coins']) ? $wallet['total_coins'] : 0 ?>
        </div>

        <!-- Nav Tabs (Bootstrap 3 format) -->
        <ul class="nav nav-tabs nav-justified" role="tablist" style="margin-top: 25px;">
            <li role="presentation" class="active">
                <a href="#rewardsTab" aria-controls="rewardsTab" role="tab" data-toggle="tab">
                    <?= translate('rewards') ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#historyTab" aria-controls="historyTab" role="tab" data-toggle="tab">
                    <?= translate('history') ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#redeemTab" aria-controls="redeemTab" role="tab" data-toggle="tab">
                    <?= translate('redeem') ?>
                </a>
            </li>
        </ul>

        <!-- Tab Panes -->
        <div class="tab-content">

            <!-- Rewards Tab -->

            <div class="tab-pane fade in active" id="rewardsTab">

                <?php if (!empty($rewards)): ?>
                    <div class="reward-grid">

                        <?php foreach ($rewards as $exam): ?>
                            <div class="panel panel-default reward-card">
                                <div class="panel-body">

                                    <div class="text-center">
                                        <div class="reward-title">
                                            <?= htmlspecialchars($exam['exam_name']); ?>
                                        </div>
                                        <div class="reward-sub">
                                            (<?= ucfirst($exam['exam_type']); ?>)
                                        </div>
                                    </div>

                                    <hr>

                                    <?php foreach ($exam['rules'] as $rule): ?>
                                        <div class="reward-rule">
                                            <span>
                                                <?php
                                                switch ($rule['reward_basis']) {
                                                    case 'rank':
                                                        echo translate('rank') . " ≤ " . intval($rule['qualifying_value']);
                                                        break;
                                                    case 'percentile':
                                                        echo translate('percentile') . " ≥ " . $rule['qualifying_value'] . "%";
                                                        break;
                                                    default:
                                                        echo translate('percentage') . " ≥ " . $rule['qualifying_value'] . "%";
                                                }
                                                ?>
                                            </span>

                                            <span class="coin-badge">
                                                +<?= (int)$rule['coin_reward']; ?> <i class="fa fa-coins"></i>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="text-center" style="margin-top:12px">
                                        <span class="scope-badge">
                                            <?= translate('reward_scope') ?>: <?= ucfirst($exam['reward_scope']); ?>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?= translate('no_reward_opportunities_found'); ?>
                    </div>
                <?php endif; ?>

            </div>


            <!-- History Tab -->
            <div role="tabpanel" class="tab-pane fade" id="historyTab">
                <?php if (!empty($history)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed mb-none">
                            <thead>
                                <tr>
                                    <th><?= translate('exam_name') ?></th>
                                    <th><?= translate('exam_type') ?></th>
                                    <th><?= translate('reward_scope') ?></th>
                                    <th><?= translate('session_code') ?></th>
                                    <th><?= translate('coins') ?></th>
                                    <th><?= translate('remarks') ?></th>
                                    <th><?= translate('date') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($h['exam_name']); ?></td>
                                        <td><span class="label label-default"><?= ucfirst($h['exam_type']); ?></span></td>
                                        <td><span class="label label-info"><?= ucfirst($h['reward_scope']); ?></span></td>
                                        <td><?= !empty($h['session_code']) ? $h['session_code'] : '-'; ?></td>
                                        <td><strong class="text-success">+<?= $h['earned_coins']; ?></strong></td>
                                        <td><?= !empty($h['remarks']) ? htmlspecialchars($h['remarks']) : '-'; ?></td>
                                        <td><?= get_nicetime($h['date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <?= translate('no_records_found'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Redeem Tab -->
            <div role="tabpanel" class="tab-pane fade" id="redeemTab">
                <div class="text-center py-4">
                    <p class="lead"><?= translate('coming_soon'); ?>...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr("href");
            $(".tab-pane").removeClass("in active");
            $(target).addClass("in active");
        });
    });
</script>