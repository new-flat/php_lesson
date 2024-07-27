<?php if ($total_results > 4) : ?>
    <!-- ◯件中◯-◯件目を表示 -->
    <p><?php echo eh($total_results); ?>件中<?php echo eh($from_record); ?>-<?php echo eh($to_record); ?>件目を表示</p>

    <!-- 前のページボタン -->
    <?php if ($page > 1) : ?>
        <a class="back_page" href="?<?php echo eh(http_build_query(array_merge($_GET, ['page' => $page - 1]))); ?>"><<</a>
    <?php else : ?>
        <span class="disabled"><<</span>
    <?php endif; ?>

    <!-- ページ番号リンク -->
    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
        <?php if ($i >= $page - $range && $i <= $page + $range) : ?>
            <?php if ($i == $page) : ?>
                <span class="current_page"><?php echo eh($i); ?></span>
            <?php else : ?>
                <a class="page_link" href="?<?php echo eh(http_build_query(array_merge($_GET, ['page' => $i]))); ?>"><?php echo eh($i); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- 次のページボタン -->
    <?php if ($page < $total_pages) : ?>
        <a class="next_page" href="?<?php echo eh(http_build_query(array_merge($_GET, ['page' => $page + 1]))); ?>">>></a>
    <?php else : ?>
        <span class="disabled">>></span>
    <?php endif; ?>
<?php endif; ?>
