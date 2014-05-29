insert into ImageDimension (
	image_set,
	default_type,
	shortname,
	title,
	max_width,
	max_height,
	crop,
	dpi,
	quality,
	strip,
	interlace,
	resize_filter,
	upscale
) values (
	(select id from ImageSet where shortname = 'inquisition-question'),
	(select id from ImageType where mime_type = 'image/jpeg'),
	'original',
	'Original',
	null,
	null,
	false,
	72,
	80,
	true,
	false,
	null,
	false
);

insert into ImageDimension (
	image_set,
	default_type,
	shortname,
	title,
	max_width,
	max_height,
	crop,
	dpi,
	quality,
	strip,
	interlace,
	resize_filter,
	upscale
) values (
	(select id from ImageSet where shortname = 'inquisition-question'),
	(select id from ImageType where mime_type = 'image/jpeg'),
	'thumb',
	'Thumbnail',
	100,
	100,
	true,
	72,
	80,
	true,
	false,
	null,
	false
);

insert into ImageDimension (
	image_set,
	default_type,
	shortname,
	title,
	max_width,
	max_height,
	crop,
	dpi,
	quality,
	strip,
	interlace,
	resize_filter,
	upscale
) values (
	(select id from ImageSet where shortname = 'inquisition-question'),
	(select id from ImageType where mime_type = 'image/jpeg'),
	'small',
	'Small',
	500,
	500,
	true,
	72,
	80,
	true,
	false,
	null,
	false
);

insert into ImageDimension (
	image_set,
	default_type,
	shortname,
	title,
	max_width,
	max_height,
	crop,
	dpi,
	quality,
	strip,
	interlace,
	resize_filter,
	upscale
) values (
	(select id from ImageSet where shortname = 'inquisition-question'),
	(select id from ImageType where mime_type = 'image/jpeg'),
	'large',
	'Large',
	1000,
	1000,
	true,
	72,
	80,
	true,
	false,
	null,
	false
);
