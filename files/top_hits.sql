load data local infile "../files/101/tmp/tmp/density_5.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
        optionally enclosed by '"'
    lines
        terminated by '\n'
    ignore
        1 lines
    (@bits, v_ind, r_density, r_naive_p, left_ind, right_ind, left_cnt, right_cnt, @density, @naive_p)
    set
        bits = cast(@bits as unsigned),
        radius_ind = 5,
        density = if(@density = "nan" or @density = "-nan", null, @density),
        naive_p = if(@naive_p = "nan" or @naive_p = "-nan", null, @naive_p);

load data local infile "../files/101/tmp/tmp/density_4.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
        optionally enclosed by '"'
    lines
        terminated by '\n'
    ignore
        1 lines
    (@bits, v_ind, r_density, r_naive_p, left_ind, right_ind, left_cnt, right_cnt, @density, @naive_p)
    set
        bits = cast(@bits as unsigned),
        radius_ind = 4,
        density = if(@density = "nan" or @density = "-nan", null, @density),
        naive_p = if(@naive_p = "nan" or @naive_p = "-nan", null, @naive_p);

load data local infile "../files/101/tmp/tmp/density_7.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
        optionally enclosed by '"'
    lines
        terminated by '\n'
    ignore
        1 lines
    (@bits, v_ind, r_density, r_naive_p, left_ind, right_ind, left_cnt, right_cnt, @density, @naive_p)
    set
        bits = cast(@bits as unsigned),
        radius_ind = 7,
        density = if(@density = "nan" or @density = "-nan", null, @density),
        naive_p = if(@naive_p = "nan" or @naive_p = "-nan", null, @naive_p);

load data local infile "../files/101/tmp/tmp/density_1.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
        optionally enclosed by '"'
    lines
        terminated by '\n'
    ignore
        1 lines
    (@bits, v_ind, r_density, r_naive_p, left_ind, right_ind, left_cnt, right_cnt, @density, @naive_p)
    set
        bits = cast(@bits as unsigned),
        radius_ind = 1,
        density = if(@density = "nan" or @density = "-nan", null, @density),
        naive_p = if(@naive_p = "nan" or @naive_p = "-nan", null, @naive_p);

load data local infile "../files/101/tmp/tmp/density_6.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
        optionally enclosed by '"'
    lines
        terminated by '\n'
    ignore
        1 lines
    (@bits, v_ind, r_density, r_naive_p, left_ind, right_ind, left_cnt, right_cnt, @density, @naive_p)
    set
        bits = cast(@bits as unsigned),
        radius_ind = 6,
        density = if(@density = "nan" or @density = "-nan", null, @density),
        naive_p = if(@naive_p = "nan" or @naive_p = "-nan", null, @naive_p);

load data local infile "../files/101/tmp/tmp/density_3.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
        optionally enclosed by '"'
    lines
        terminated by '\n'
    ignore
        1 lines
    (@bits, v_ind, r_density, r_naive_p, left_ind, right_ind, left_cnt, right_cnt, @density, @naive_p)
    set
        bits = cast(@bits as unsigned),
        radius_ind = 3,
        density = if(@density = "nan" or @density = "-nan", null, @density),
        naive_p = if(@naive_p = "nan" or @naive_p = "-nan", null, @naive_p);

load data local infile "../files/101/tmp/tmp/density_2.csv"
    into table Module_101.report_top_hits
    fields
        terminated by ','
