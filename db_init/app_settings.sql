USE curatedsubs;


INSERT INTO `AppSettingsModel` (`id`, `imprint`, `privacy`, `app`, `about`, `age_consent`, `info`, `info_style`, `head_code`, `categories`, `created_at`) VALUES
(1, 'Imprint', 'Privacy Policy', 'App', 'About', 'This is a site for curated content. Do you want to proceed?', '', '', '', 'reddit,curated', now());

INSERT INTO `SubsModel` (`id`, `sub_ident`, `category`, `description`, `cat_order`, `cat_video`, `featured`, `twitter_posting`, `last_check`, `last_desc`, `created_at`) VALUES
(1, 'r/empty', 'empty', 'You\'ll know it when you see it.', 1, 1, 1, 0, '2025-03-17 16:33:44', '2025-04-25 07:50:10', '2025-01-23 14:33:45'),
