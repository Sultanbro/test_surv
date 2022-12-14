<script>
import NewsPages from '@/pages/News/NewsPages'
import BirthdayFeed from '@/pages/News/BirthdayFeed'
import DefaultLayout from '@/layouts/DefaultLayout'

export default {
    name: 'NewsView',
    components: {
        DefaultLayout,
        NewsPages,
        BirthdayFeed,
    }
}
</script>

<template>
    <DefaultLayout>
        <div class="news-page">
            <NewsPages
                page="news"
                access="edit"
            />
            <BirthdayFeed/>
        </div>
    </DefaultLayout>
</template>