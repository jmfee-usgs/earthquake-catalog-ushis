# SRA CATALOG OF EARTHQUAKES IN THE EASTERN, CENTRAL, AND MOUNTAIN STATES OF THE UNITED STATES, 1568 - 1986

‘SRA’ comes from the last names of the principal
compilers, C.W. Stover, B.G. Reagor, and S.T. Algermissen. For U.S. states east
of, and including, Idaho, Utah, and Arizona, the catalog contains origins of
earthquakes that were also plotted in a series of state-specific USGS
Miscellaneous Field Studies Maps published in 1977-1988.  Prior to 2012, the
version of the catalog that was searchable at the USGS/NEIC web-site also
included many events from the Pacific states and Nevada, but this westernmost
part of the catalog was still a work-in-progress at the time that compilation
of the catalog was suspended. The current catalog does not include events from
Nevada or Pacific Coast states.  For the eastern, central, and mountain states
for which compilation and review of the catalog were completed, the database is
particularly valuable as a compilation of locations, magnitudes, and intensities
of small, non-instrumentally-recorded, earthquakes that are omitted from many
other earthquake listings. For most large or damaging U.S. earthquakes, the
USHIS catalog would be considered authoritative instead of the SRA catalog.

In states for which catalogs in the above-cited USGS Miscellaneous Field Studies
Maps did not extend to 1986, the compilers of SRA database updated the file
through 1986 with origins from the USGS National Earthquake Information Center
Preliminary Determination of Epicenters publication.  Finally, the entries for
some individual earthquakes that were covered in one of the preceding state maps
have been updated on the basis of information obtained between publication of
the map and compilation of the database.


## Files in this directory
- `sra/README.md`: this file
- `sra/sra.csv`: CSV formatted USHIS catalog
- `sra/sra_documentation.csv`: description of fields in `sra.csv`
- `sra/sra2quakeml.php`: script to parse CSV format and output quakeml

## Usage
```cat sra.csv | php sra2quakeml.php```
