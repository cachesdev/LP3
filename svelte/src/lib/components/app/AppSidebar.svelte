<script lang="ts">
	import Calendar from 'lucide-svelte/icons/calendar';
	import House from 'lucide-svelte/icons/house';
	import Inbox from 'lucide-svelte/icons/inbox';
	import Search from 'lucide-svelte/icons/search';
	import Settings from 'lucide-svelte/icons/settings';
	import { type Icon } from 'lucide-svelte';
	import * as Sidebar from '$lib/components/ui/sidebar/index.js';
	import type { ComponentType } from 'svelte';

	type Item = {
		title: string;
		url: string;
		icon: ComponentType<Icon>;
		active: boolean;
	};

	// Menu items.
	const items: Item[] = $state([
		{
			title: 'Home',
			url: '#',
			icon: House,
			active: false
		},
		{
			title: 'Inbox',
			url: '#',
			icon: Inbox,
			active: false
		},
		{
			title: 'Calendar',
			url: '#',
			icon: Calendar,
			active: false
		},
		{
			title: 'Search',
			url: '#',
			icon: Search,
			active: false
		},
		{
			title: 'Settings',
			url: '#',
			icon: Settings,
			active: false
		}
	]);

	const handleActive = (item: Item) => {
		items.map((el) => {
			if (el.title === item.title) {
				el.active = true;
			} else {
				el.active = false;
			}
		});
	};
</script>

<Sidebar.Root>
	<Sidebar.Content>
		<Sidebar.Group>
			<Sidebar.GroupLabel>Application</Sidebar.GroupLabel>
			<Sidebar.GroupContent>
				<Sidebar.Menu>
					{#each items as item (item.title)}
						<Sidebar.MenuItem>
							<Sidebar.MenuButton isActive={item.active}>
								{#snippet child({ props })}
									<a onclick={() => handleActive(item)} href={item.url} {...props}>
										<item.icon />
										<span>{item.title}</span>
									</a>
								{/snippet}
							</Sidebar.MenuButton>
						</Sidebar.MenuItem>
					{/each}
				</Sidebar.Menu>
			</Sidebar.GroupContent>
		</Sidebar.Group>
	</Sidebar.Content>
</Sidebar.Root>
