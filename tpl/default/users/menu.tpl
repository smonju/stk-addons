{if empty($users)}
    <p class="text-info">{t}No users match your search{/t}</p>
{else}
    {if !isset($current_user)}
        {$current_user=""}
    {/if}
    <div class="list-group">
        {foreach $users as $user}
            {$disabled=""} {$active=""}
            {if !$user.active}
                {$disabled=" disabled"}
            {/if}
            {if $user.username === $current_user}
                {$active=" active"}
            {/if}
            <a href="users.php?user={$user.username}" class="list-group-item user-list{$disabled}{$active}">
                <img class="icon" src="{$img_location}user.png">
                <span>{$user.username|truncate:28}</span>
            </a>
        {/foreach}
    </div>
{/if}
{$pagination}