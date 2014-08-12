earthquake-catalog-ushis
========================

This project contains code to convert the "USHIS" catalog to Quakeml format.

Files in this project:
- ```ushis.csv```: CSV formatted USHIS catalog
- ```ushis_documentation.csv```: description of fields in ```ushis.csv```
- ```ushis2quakeml.php```: script to parse CSV format and output quakeml

Usage:
```cat ushis.csv | php ushis2quakeml.php```


USHIS is a catalog of moderate and large earthquakes in the United States, from
1638-1989.  This catalog is from the publication of Stover, C.W. and Coffman,
J. L., 1993, Seismicity of the United States, 1568 – 1989, U.S. Geological
Survey Professional Paper 1527, 418 pp.

For on-land areas of states exclusive of Alaska, the catalog tabulates
earthquakes with magnitudes greater than or equal to 4.5 or intensity of VI or
larger. The magnitude threshold is increased to 5.5 or larger for Alaska and
for offshore areas of California, Oregon, and Washington. Some earthquakes
whose epicenters were in Canada or Mexico are included if these shocks produced
intensity VI effects in the U.S. Shocks listed are those for which Stover and
Coffman could identify the time of occurrence to the nearest day: in their
publication, Stover and Coffman discuss additional shocks (earliest, 1568) for
which only the year of occurrence is known. The publication of Stover and
Coffman contains brief narratives of the damage caused by earthquakes that
produced effects of intensity VI or larger. Epicenters, origin-times, and
magnitudes for pre-twentieth century earthquakes, as well as some shocks in the
early and mid-twentieth century, are inferred from human observations of the
earthquakes’ effects and are generally not as accurate as epicenters,
origin-times, and magnitudes that were determined for later shocks on the basis
of seismographic data.
