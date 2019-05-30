<?php echo !empty($title) ? "<h3>{$title}</h3>" : ''; ?>
<?php if (empty($values)): ?>

    <pre>Empty.</pre>

<?php else: ?>

    <table class="table table-condensed table-bordered table-striped table-hover request-table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th>字段名</th>
                <th style="width: 160px;">必须</th>
                <th style="width: 160px;">类型</th>
                <th>注释</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($values as $value): ?>
            <tr>
                <td><?php echo empty($value['name']) ? '' : $value['name']; ?></td>
                <td><?php echo empty($value['is_necessary']) ? 'false' : 'true'; ?></td>
                <td><?php echo empty($value['type']) ? '' : $value['type']; ?></td>
                <td><?php echo empty($value['desc']) ? '' : $value['desc']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>