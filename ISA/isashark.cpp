/*
*   ISA project
*   Packet Analyzer
*   Author: Martin Bobcik, xbobci00
*   Date: 19. Nov 2017
*/

#include <algorithm>
#include <stdlib.h>
#include <ctype.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <iostream>
#include <iomanip>
#include <netinet/in.h>
#include <netinet/ip6.h>
#include <netinet/ether.h>
#include <netinet/ip.h>
#include <netinet/tcp.h>
#include <netinet/udp.h>
#include <netinet/icmp6.h>
#include <netinet/ip_icmp.h>
#include <netinet/if_ether.h>
#include <sys/time.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <pcap.h>
#include <vector>
#include <cstdio>
#include <string>
#include <err.h>
#include <time.h>
#include <pcap/pcap.h>
#include <map>
#include <fstream>

using namespace std;

#ifndef PCAP_ERRBUF_SIZE
#define PCAP_ERRBUF_SIZE (256)
#endif
#ifndef SIZE_ETHERNET
#define SIZE_ETHERNET (14)
#endif
#ifndef SIZE_VLAN
#define SIZE_VLAN (4)
#endif
#ifndef ICMP_PROTOCOL
#define ICMP_PROTOCOL (1)    
#endif
#ifndef ICMPv6_PROTOCOL
#define ICMPv6_PROTOCOL (58)
#endif
#ifndef TCP_PROTOCOL
#define TCP_PROTOCOL (6)
#endif
#ifndef UDP_PROTOCOL
#define UDP_PROTOCOL (17)
#endif

//      CREDITS: https://stackoverflow.com/a/20518995/7702486
#define MAC2STR(a) (a)->ether_addr_octet[0], (a)->ether_addr_octet[1], (a)->ether_addr_octet[2], (a)->ether_addr_octet[3], (a)->ether_addr_octet[4], (a)->ether_addr_octet[5]
#define MACSTR "%02x:%02x:%02x:%02x:%02x:%02x"
//      END CREDITS

typedef struct TCPorUDPStruct{
    uint dstPort;
    uint srcPort;
    uint seqNumber;
    uint ackNumber;
    string Flags;  // maybe more
} TCPorUDPStruct;

typedef struct ICMPStruct
{
    uint code;
    uint type;
} ICMPStruct;

typedef struct packetStruct
{
    uint packetNumber;
    uint packetLength;
    time_t timeStamp;
    char * srcMac;
    char * dstMac;
    std::vector<uint>  VLANid;
    int l3type;
    char * srcIp;
    int TTLorHOP;
    char * dstIp;
    int l4type;
    TCPorUDPStruct TCPorUDP;
    ICMPStruct ICMP;
} packetStruct;

typedef struct aggrStruct
{
    int aggrPackets;
    int aggrBytes;
} aggrStruct;

typedef struct {
    string aggr;
    string sort;
    string limit;
    string filter;
    vector<string> files;
} Params;

typedef std::map<int, string> typesMap;
typedef std::map<std::pair<uint, uint>, string> ICMPMap;
typedef std::map<string, aggrStruct> agregationMap;
typedef std::map<int, packetStruct> packetMap; 

void PrintHelp(){
    printf("Program reads packets from *.pcap files and prints its protocols and various values from those protocols.\n\n");
    printf("Supported protocols:\n\t-Ethernet-IEEE 802.1Q\n\t-IEEE 802.1ad\n\t-IPv4\n\t-IPv6\n\t-ICMPv4\n\t-ICMPv6\n\t-TCP\n\t-UDP\n\n");
    printf("Limitations:\n\tProgram does not support IPv4 nor IPv6 packet fragmentation.\n\n");
    printf("Usage:\n\tisashark [-h] [-a aggr-key] [-s sort-key] [-l limit] [-f filter-expression] file ...\n\t");
    printf("-h Print this help.\n\t");
    printf("-a aggr-key Packet aggregation by aggr-key. If aggr-key is not supported, program will end with exitcode 3.\n\t\t");
    printf("- srcmac Source MAC address.\n\t\t");
    printf("- dstmac Destination MAC address.\n\t\t");
    printf("- srcip Source IP address.\n\t\t");
    printf("- dstip Destination IP address.\n\t\t");
    printf("- srcport Source port address (only for TCP and UDP packets).\n\t\t");
    printf("- dstport Destination port address (only for TCP and UDP packets).\n\t");
    printf("-s sort-key Output is printed in descending order. Packets are ordered by sort-key.\n\t\t\
     If sort-key is not supported, program will end with exitcode 3.\n\t\t");
    printf("- packets Sorting by count of packets (only supported with aggregation).\n\t\t");
    printf("- bytes Sorting by count of bytes.\n\t");
    printf("-l limit Program will print only limit of records. limit is type of unsigned integer, \n\t\t\
     if not, program will end with exitcode 3.\n\t");
    printf("-f filter-expression Program will process only packets satisfying pcap filter given as filter-expression.\n\t\t\
     If filter-expression is not pcap filter, program wil end with exitcode 2.\n\t");
    printf("file Path to pcap files to process. If files can not be opened or do not exist,\n\t\t program will end with exitcode 1.\n");
    exit(0);
}

void PrintParams(Params params){
    cout << "Params:\n";
    cout << "\taggregate:  " << params.aggr <<endl;
    cout << "\tsort:  " << params.sort <<endl;
    cout << "\tlimit:  " << params.limit <<endl;
    cout << "\tfilter:  " << params.filter <<endl;

    printf("\tfiles:\n");

    for (vector<string>::iterator t = params.files.begin(); t != params.files.end(); ++t)
    {
        cout<< "\t\t" <<*t<<endl;
    }
}

bool checkLimit(string limit){
    for( uint i = 0; i < limit.length(); i++){
        if(!isdigit(limit.at(i))){
            return false;
        }
    }
    return true;
}

Params ProcessParams(int argc, char **argv){
    Params result;
    int c;
    while ((c = getopt(argc,argv, "ha:s:l:f:")) != -1){
        switch (c){
            case 'h':
            PrintHelp();
            break;
            case 'a':
            result.aggr = string(optarg);
            break;
            case 's':
            result.sort = string(optarg);
            break;
            case 'l':
            result.limit = string(optarg);
            break;
            case 'f':
            result.filter = string(optarg);
            break;
            case '?':
            PrintHelp();
            break;
            default:
            abort();
            break;
        }
    }
    // store file paths
    for (int index = optind; index < argc; index++)
        result.files.push_back(argv[index]);

    //check limit parameter
    if(checkLimit(result.limit) == false)
        exit(3);

    //check sort parameter
    if ( result.sort.length() > 0 )
    {
        if(!(result.sort.compare("bytes") == 0 || result.sort.compare("packets") == 0))
        {
            exit(3);
        }
    }
    return result;
}

bool file_exist(string fileName){
    std::ifstream handle;
    handle.open(fileName,std::ifstream::in);
    return handle.good();
}

uint getVlanIDFromXthVlanHeader(const u_char *packet, int x){
    char * withoutEther;
    withoutEther = (char *)(packet + SIZE_ETHERNET + (SIZE_VLAN * x )); // get vlan header 
    uint id; 
    id = withoutEther[0]; // store firstpart of id
    id = id << 8;         //shift id to make space for second part
    uint nextpart = withoutEther[1] & 255; //store second part and map out posible top bits
    id |= nextpart;  // compose both parts
    // map out top 4 bits
    //   0000 1111  1111 1111
    int map = 4095;
    id = id & map; // map out posible top bits
    return id;
}

uint getNextTypeFromXthVlanHeader(const u_char *packet, int x){
    char * withoutEther;
    withoutEther = (char *)(packet + SIZE_ETHERNET + (SIZE_VLAN * x )); // get vlan header
    uint nextType = 0;     
    nextType = withoutEther[2]; //store first part of next type
    nextType = nextType << 8;   // shift to make space
    uint nextpart = withoutEther[3] & 255; //store second part and map out posible top bits
    nextType |= nextpart;   // compose both parts
    nextType = nextType & 65535; // map out top bits
    return nextType;
}

//my super thoughtful method to convert flags to string
/* obsolete */
string TCPFlagsToString(u_char flags){
    string result = "";
    //string FlagList = "CEUAPRSF";
    string FlagList = "FSRPAUEC";

    for (int i = 7; i >=0; --i)
    {
        if (flags & (2 << (i - 1)))
        {
            result.push_back(FlagList[i]);
        } else {
            result.push_back('.');
        }
    }
    return result;
}

void fillL3Map(typesMap &map){
    map.insert(typesMap::value_type(ETHERTYPE_IP, "IPv4"));
    map.insert(typesMap::value_type(ETHERTYPE_IPV6, "IPv6"));
}

void fillL4Map(typesMap &map){
    map.insert(typesMap::value_type(ICMP_PROTOCOL, "ICMP"));
    map.insert(typesMap::value_type(ICMPv6_PROTOCOL, "ICMPv6"));
    map.insert(typesMap::value_type(TCP_PROTOCOL, "TCP"));
    map.insert(typesMap::value_type(UDP_PROTOCOL, "UDP"));
}

void fillICMPMap(ICMPMap &map){
    map.insert(ICMPMap::value_type(make_pair(0, 0), "0 0 echo reply"));

    map.insert(ICMPMap::value_type(make_pair(3, 0), "3 0 destination unreachable net unreachable"));
    map.insert(ICMPMap::value_type(make_pair(3, 1), "3 1 destination unreachable host unreachable"));
    map.insert(ICMPMap::value_type(make_pair(3, 2), "3 2 destination unreachable protocol unreachable"));
    map.insert(ICMPMap::value_type(make_pair(3, 3), "3 3 destination unreachable port unreachable"));
    map.insert(ICMPMap::value_type(make_pair(3, 4), "3 4 destination unreachable fragmentation needed and DF set"));
    map.insert(ICMPMap::value_type(make_pair(3, 5), "3 5 destination unreachable source route failed"));

    map.insert(ICMPMap::value_type(make_pair(4, 0), "4 0 source quench"));

    map.insert(ICMPMap::value_type(make_pair(5, 0), "5 0 redirect message redirect datagram for the network"));
    map.insert(ICMPMap::value_type(make_pair(5, 1), "5 1 redirect message redirect datagram for the host"));
    map.insert(ICMPMap::value_type(make_pair(5, 2), "5 2 redirect message redirect datagram for the service and network"));
    map.insert(ICMPMap::value_type(make_pair(5, 3), "5 3 redirect message redirect datagram for the service and host"));

    map.insert(ICMPMap::value_type(make_pair(8, 0), "8 0 echo request"));

    map.insert(ICMPMap::value_type(make_pair(11, 0), "11 0 time exceeded time to live exceeded in transit"));
    map.insert(ICMPMap::value_type(make_pair(11, 1), "11 1 time exceeded fragment reassembly time exceeded"));

    map.insert(ICMPMap::value_type(make_pair(12, 0), "12 0 parameter problem pointer indicates the error"));

    map.insert(ICMPMap::value_type(make_pair(13, 0), "13 0 timestamp"));
    map.insert(ICMPMap::value_type(make_pair(14, 0), "14 0 timestamp reply"));

    map.insert(ICMPMap::value_type(make_pair(15, 0), "15 0 information request message"));
    map.insert(ICMPMap::value_type(make_pair(16, 0), "16 0 information reply message"));
}

void fillICMPHalfMap(std::map<uint, string> &map){
    map.insert(make_pair(0,"echo reply"));
    map.insert(make_pair(3,"destination unreachable"));
    map.insert(make_pair(4,"source quench"));
    map.insert(make_pair(5,"redirect message"));
    map.insert(make_pair(8,"echo request"));
    map.insert(make_pair(11,"time exceeded"));
    map.insert(make_pair(12,"parameter problem"));
    map.insert(make_pair(13,"timestamp"));
    map.insert(make_pair(14,"timestamp reply"));
    map.insert(make_pair(15,"information request message"));
    map.insert(make_pair(16,"information reply message"));
}

void fillICMPv6Map(ICMPMap &map){
    map.insert(ICMPMap::value_type(make_pair(1, 0), "1 0 destination unreachable no route to destination"));
    map.insert(ICMPMap::value_type(make_pair(1, 1), "1 1 destination unreachable communication with destination administratively prohibited"));
    map.insert(ICMPMap::value_type(make_pair(1, 2), "1 2 destination unreachable beyond scope of source address"));
    map.insert(ICMPMap::value_type(make_pair(1, 3), "1 3 destination unreachable address unreachable"));
    map.insert(ICMPMap::value_type(make_pair(1, 4), "1 4 destination unreachable port unreachable"));
    map.insert(ICMPMap::value_type(make_pair(1, 5), "1 5 destination unreachable source address failed ingress/egress policy"));
    map.insert(ICMPMap::value_type(make_pair(1, 6), "1 6 destination unreachable reject route to destination"));
    map.insert(ICMPMap::value_type(make_pair(1, 7), "1 7 destination unreachable error in source routing header"));
    
    map.insert(ICMPMap::value_type(make_pair(2, 0), "2 0 packet too big"));

    map.insert(ICMPMap::value_type(make_pair(3, 0), "3 0 time exceeded hop limit exceeded in transit"));
    map.insert(ICMPMap::value_type(make_pair(3, 1), "3 1 time exceeded fragment reassembly time exceeded"));

    map.insert(ICMPMap::value_type(make_pair(4, 0), "4 0 parameter problem erroneous header field encountered"));
    map.insert(ICMPMap::value_type(make_pair(4, 1), "4 1 parameter problem unrecognized next header type encountered"));
    map.insert(ICMPMap::value_type(make_pair(4, 2), "4 2 parameter problem unrecognized IPv6 option encountered"));

    map.insert(ICMPMap::value_type(make_pair(128, 0), "128 0 echo request"));
    map.insert(ICMPMap::value_type(make_pair(129, 0), "129 0 echo reply"));
}

void fillICMPv6HalfMap(std::map<uint, string> &map){
    map.insert(make_pair(1,"destination unreachable"));
    map.insert(make_pair(2,"packet too big"));
    map.insert(make_pair(3,"time exceeded"));
    map.insert(make_pair(4,"parameter problem"));
    map.insert(make_pair(128,"echo request"));
    map.insert(make_pair(129,"echo reply"));
}

void fillIPv6ExtensionHeaderCodesVector(std::vector<int> &vec){
    vec.push_back(0);
    vec.push_back(60);
    vec.push_back(43);
    vec.push_back(51);
    vec.push_back(50);
    vec.push_back(135);
    vec.push_back(139);
    vec.push_back(140);
}

void printPacket(packetStruct ps){
    // maps for translating icmp types and codes
    ICMPMap ICMPTypesMap;
    fillICMPMap(ICMPTypesMap);
    ICMPMap ICMPv6TypesMap;
    fillICMPv6Map(ICMPv6TypesMap);
    std::map<uint, string> ICMPHalfMap;
    fillICMPHalfMap(ICMPHalfMap);
    std::map<uint, string> ICMPv6HalfMap;
    fillICMPv6HalfMap(ICMPv6HalfMap);

    string toPrint = to_string(ps.packetNumber) + ": " + to_string(ps.timeStamp) + " " + to_string(ps.packetLength) + " | Ethernet: ";
    toPrint += string(ps.srcMac) + " " + string(ps.dstMac) + " ";

    for (uint i = 0; i < ps.VLANid.size(); i++)
    {
        toPrint+= to_string(ps.VLANid.at(i)) + " ";
    }

    if (ps.l3type == ETHERTYPE_IP){
        toPrint += "| IPv4: ";
    } else {
        toPrint += "| IPv6: ";
    }
    toPrint += string(ps.srcIp) + " " + string(ps.dstIp) + " " + to_string(ps.TTLorHOP) + " | ";

    if (ps.l4type == TCP_PROTOCOL)
    {
        toPrint += "TCP: " + to_string(ps.TCPorUDP.srcPort) + " " + to_string(ps.TCPorUDP.dstPort) + " " + to_string(ps.TCPorUDP.seqNumber);
        toPrint += " " + to_string(ps.TCPorUDP.ackNumber) + " " + ps.TCPorUDP.Flags;
    } else if (ps.l4type == ICMP_PROTOCOL){
        if (ICMPTypesMap.count(make_pair(ps.ICMP.type, ps.ICMP.code)) != 0){    //if both type and code are known
            toPrint += "ICMPv4: " + ICMPTypesMap.at(make_pair(ps.ICMP.type, ps.ICMP.code));
        } else if(ICMPHalfMap.count(ps.ICMP.type) != 0){                        // only type is known
            toPrint += "ICMPv4: " + to_string(ps.ICMP.type) + " " + to_string(ps.ICMP.code) + " " + ICMPHalfMap.at(ps.ICMP.type);
        } else {                                                                //unknown type and code
            toPrint += "ICMPv4: " + to_string(ps.ICMP.type) + " " + to_string(ps.ICMP.code);    
        }
    } else if (ps.l4type == ICMPv6_PROTOCOL){
        if (ICMPv6TypesMap.count(make_pair(ps.ICMP.type, ps.ICMP.code)) != 0){  //if both type and code are known
            toPrint += "ICMPv6: " + ICMPv6TypesMap.at(make_pair(ps.ICMP.type, ps.ICMP.code));
        } else if(ICMPv6HalfMap.count(ps.ICMP.type) != 0){                       // only type is known
            toPrint += "ICMPv6: " + to_string(ps.ICMP.type) + " " + to_string(ps.ICMP.code) + " " + ICMPv6HalfMap.at(ps.ICMP.type);
        } else {                                                                //unknown type and code
            toPrint += "ICMPv6: " + to_string(ps.ICMP.type) + " " + to_string(ps.ICMP.code) ;    
        }    
    } else if (ps.l4type == UDP_PROTOCOL){
        toPrint += "UDP: " + to_string(ps.TCPorUDP.srcPort) + " " + to_string(ps.TCPorUDP.dstPort);
    }
    cout << toPrint <<endl;
}

agregationMap agregate(packetMap pm, string aggrMode){
    agregationMap aggrMap;
    //iterate through all packets
    for (auto pmIterator = pm.begin(); pmIterator != pm.end(); pmIterator++)
    {
        packetStruct ps = pmIterator->second;
        //get key from packet, according to aggr-key
        string key;
        if(aggrMode.compare("srcmac") == 0){ // if aggregating src macs
            key = ps.srcMac;
        } else if(aggrMode.compare("dstmac") == 0){
            key = ps.dstMac;
        } else if(aggrMode.compare("srcip") == 0){
            key = ps.srcIp;
        } else if(aggrMode.compare("dstip") == 0){
            key = ps.dstIp;
        } else if(aggrMode.compare("srcport") == 0){
            if(ps.l4type == TCP_PROTOCOL || ps.l4type == UDP_PROTOCOL){
                key =to_string(ps.TCPorUDP.srcPort);
            }
            else{
                continue;
            }
        } else if(aggrMode.compare("dstport") == 0){
            if(ps.l4type == TCP_PROTOCOL || ps.l4type == UDP_PROTOCOL){
                key = to_string(ps.TCPorUDP.dstPort);
            }else{
                continue;
            }
        } else{
            exit(3);    //bad aggr type
        }

        if(aggrMap.count(key) == 0){    // if mac not in map
            aggrStruct as;                
            as.aggrPackets = 1;                // insert in map
            as.aggrBytes = ps.packetLength;
            aggrMap[key] = as;
        }
        else{    
            aggrMap[key].aggrPackets++;  // add to existing
            aggrMap[key].aggrBytes += ps.packetLength;
        }
    }
    return aggrMap;
}

void printOneAgregate(string key, aggrStruct as){
    string toPrint = key + ": " + to_string(as.aggrPackets) + " " + to_string(as.aggrBytes);
    cout<< toPrint << endl;
}

int main(int argc, char **argv) {
    Params params = ProcessParams(argc,argv);
    //PrintParams(params);

      // map for holding packets
    packetMap pm;

       // maps for translating l3 and l4 protocols from numbers to actual names
    typesMap l3typesMap;
    fillL3Map(l3typesMap);
    typesMap l4typesMap;
    fillL4Map(l4typesMap);

       //vector for storing ipv6 extension header numbers
    std::vector<int> ipv6ExtensionHeaderCodes;
    fillIPv6ExtensionHeaderCodesVector(ipv6ExtensionHeaderCodes); 

    pcap_t *handle; // file handle
    struct bpf_program fp;          // the compiled filter
    unsigned int currentFileNumber = 0; // file counter
    struct pcap_pkthdr header; // packet header
    const u_char *packet; // current packet
    unsigned int packetCount = 0; // number of packets

    //structures for holding protocol headers
    struct ether_header *eptr; // ethernet packet header structure
    struct ip *ipPart;
    struct ip6_hdr *ip6Part;
    struct icmp *icmpHeader;
    struct icmp6_hdr *icmpv6Header;
    struct tcphdr *tcpHeader;
    struct udphdr *udpHeader;

    //open all files
    while (currentFileNumber < params.files.size()) {
        //if file does not exist, exit with error
        if(!file_exist(params.files[currentFileNumber])){
            exit(1);
        }

        //open pcap file
        if ((handle = pcap_open_offline(params.files[currentFileNumber].c_str(), NULL)) == NULL)
            err(1, "Can't open file %s for reading", argv[1]);    // if can not open file for pcap reading, throw error

        // compile the filter
        if (pcap_compile(handle, &fp, params.filter.c_str(), 0, PCAP_NETMASK_UNKNOWN) == -1)
            err(2,"pcap_compile() failed");

        // set the filter to the packet capture handle
        if (pcap_setfilter(handle,    &fp) == -1)
            err(2,"pcap_setfilter() failed");

        currentFileNumber++;

        //read packets
        while ((packet = pcap_next(handle,&header)) != NULL){
            // storing if packet is forgetable
            bool forgetPacket = false;
            // custom structure for packer
            packetStruct ps;

            // ethernet header
            eptr = (struct ether_header *) packet;
            ps.packetLength = header.len;
            ps.timeStamp = ((1000000 * header.ts.tv_sec) + header.ts.tv_usec);
            ps.dstMac = (char *)malloc(sizeof(char) * 15);
            ps.srcMac = (char *)malloc(sizeof(char) * 15);
            sprintf(ps.dstMac, MACSTR, MAC2STR( (const struct ether_addr *)&eptr->ether_dhost)); 
            sprintf(ps.srcMac, MACSTR, MAC2STR( (const struct ether_addr *)&eptr->ether_shost));

            // storing actualy covered length of packet
            uint coveredLenthOfPacket = SIZE_ETHERNET;
            uint nextType = ntohs(eptr->ether_type); 
            if (nextType == ETHERTYPE_VLAN || nextType == 0x88a8)        
            {
                int actualVlanHeader = 0;
                while ( nextType == ETHERTYPE_VLAN || nextType == 0x88a8){

                    ps.VLANid.push_back(getVlanIDFromXthVlanHeader(packet, actualVlanHeader));
                    nextType = getNextTypeFromXthVlanHeader(packet, actualVlanHeader);
                    actualVlanHeader++;
                    // TODO osetrit nezname headery
                }
                coveredLenthOfPacket += SIZE_VLAN * actualVlanHeader;
            }

            switch (nextType){
                case ETHERTYPE_IP://V4                                    // fragmentation somewhere here
                    ps.l3type = ETHERTYPE_IP;
                    ipPart = (struct ip*) (packet + coveredLenthOfPacket);
                        // ip = 3 numbers * 4 octets + 3 dots
                    ps.srcIp = (char *)malloc(3 * 4 * sizeof(char) + 3);
                    ps.dstIp = (char *)malloc(3 * 4 * sizeof(char) + 3);
                    sprintf(ps.srcIp, "%s", inet_ntoa(ipPart->ip_src));
                    sprintf(ps.dstIp, "%s", inet_ntoa(ipPart->ip_dst));
                    ps.TTLorHOP = ipPart->ip_ttl;
                    ps.l4type = ipPart->ip_p;
                    coveredLenthOfPacket += ipPart->ip_hl * 4;
                    break;
                case ETHERTYPE_IPV6:
                    ps.l3type = ETHERTYPE_IPV6;
                    ip6Part = (struct ip6_hdr*) (packet + coveredLenthOfPacket);
                        // ipv6 = 4 alnums * 8 groups + 7 colons
                    ps.srcIp = (char *)malloc(8 * 4 * sizeof(char) + 7);
                    ps.dstIp = (char *)malloc(8 * 4 * sizeof(char) + 7);
                    inet_ntop(AF_INET6, &(ip6Part->ip6_src), ps.srcIp, 8 * 4 * sizeof(char) + 7);
                    inet_ntop(AF_INET6, &(ip6Part->ip6_dst), ps.dstIp, 8 * 4 * sizeof(char) + 7);
                    ps.TTLorHOP = ip6Part->ip6_hops;
                    ps.l4type = ip6Part->ip6_nxt;
                    coveredLenthOfPacket += 40;   //?? I counted 40 octets in IPv6, so...
                    
                    // cycle over posible additional headers
                    while(count(ipv6ExtensionHeaderCodes.begin(), ipv6ExtensionHeaderCodes.end(), ps.l4type) > 0){
                        struct ip6_ext *ExtensionHdr = (struct ip6_ext*)(packet + coveredLenthOfPacket);
                        ps.l4type = ExtensionHdr->ip6e_nxt;
                        coveredLenthOfPacket += ExtensionHdr->ip6e_len + 1;
                    }
                    break;
                default:
                    fprintf(stderr, "Unknown or not supported L3 protocol: %d\n", nextType);
                    forgetPacket = true;
                    break;
            }

            switch (ps.l4type){
                case ICMP_PROTOCOL:
                    if (ps.l3type == ETHERTYPE_IPV6)
                    {   
                        forgetPacket = true;
                    }
                    icmpHeader = (struct icmp *)(packet + coveredLenthOfPacket);
                    ps.ICMP.type = icmpHeader->icmp_type;
                    ps.ICMP.code = icmpHeader->icmp_code;
                    break;
                case ICMPv6_PROTOCOL:
                    if (ps.l3type == ETHERTYPE_IPV6)
                    {   
                        forgetPacket = true;
                    }
                    icmpv6Header = (struct icmp6_hdr *)(packet + coveredLenthOfPacket);
                    ps.ICMP.type = icmpv6Header->icmp6_type;
                    ps.ICMP.code = icmpv6Header->icmp6_code;
                    break;
                case TCP_PROTOCOL:
                    tcpHeader = (struct tcphdr *)(packet + coveredLenthOfPacket);
                    ps.TCPorUDP.srcPort = ntohs(tcpHeader->source);
                    ps.TCPorUDP.dstPort = ntohs(tcpHeader->dest);
                    ps.TCPorUDP.seqNumber = ntohl(tcpHeader->seq);
                    ps.TCPorUDP.ackNumber = ntohl(tcpHeader->ack_seq);

                    tcpHeader->res2&1 ? ps.TCPorUDP.Flags.push_back('C') : ps.TCPorUDP.Flags.push_back('.');    //u_int16_t res2:2;    
                    tcpHeader->res2&2 ? ps.TCPorUDP.Flags.push_back('E') : ps.TCPorUDP.Flags.push_back('.');        
                    tcpHeader->urg ? ps.TCPorUDP.Flags.push_back('U') : ps.TCPorUDP.Flags.push_back('.');        //u_int16_t urg:1;
                    tcpHeader->ack ? ps.TCPorUDP.Flags.push_back('A') : ps.TCPorUDP.Flags.push_back('.');        //u_int16_t ack:1;
                    tcpHeader->psh ? ps.TCPorUDP.Flags.push_back('P') : ps.TCPorUDP.Flags.push_back('.');        //u_int16_t psh:1;
                    tcpHeader->rst ? ps.TCPorUDP.Flags.push_back('R') : ps.TCPorUDP.Flags.push_back('.');        //u_int16_t rst:1;
                    tcpHeader->syn ? ps.TCPorUDP.Flags.push_back('S') : ps.TCPorUDP.Flags.push_back('.');        //u_int16_t syn:1;
                    tcpHeader->fin ? ps.TCPorUDP.Flags.push_back('F') : ps.TCPorUDP.Flags.push_back('.');        //u_int16_t fin:1;

                    //ps.TCPorUDP.Flags = TCPFlagsToString(tcpHeader->th_flags);
                    break;
                case UDP_PROTOCOL:
                    udpHeader = (struct udphdr *)(packet + coveredLenthOfPacket);
                    ps.TCPorUDP.srcPort = ntohs(udpHeader->source);
                    ps.TCPorUDP.dstPort = ntohs(udpHeader->dest);
                    break;
                default:
                    fprintf(stderr, "Unknown or not supported L4 protocol: %d\n", ps.l4type);
                    forgetPacket = true;
                    break;
            }

            if(!forgetPacket){    //if correct packet, store it
                packetCount++;
                ps.packetNumber = packetCount;
                pm[ps.packetNumber] = ps;
            }
        }
        pcap_close(handle);
    }

    // if aggregation is on
    if(!params.aggr.empty()){
        agregationMap aggrMap = agregate(pm, params.aggr);

        //if sorting is off
        if(params.sort.empty()){ // print agregated packets and end
            uint printCount = 0;    
            uint limit=0;
            if(!params.limit.empty())
                limit = stoul(params.limit, NULL,10);

            for(auto amIterator = aggrMap.begin(); amIterator != aggrMap.end(); amIterator++){
                printOneAgregate(amIterator->first, amIterator->second);
                printCount++;

                if(printCount >= limit && !params.limit.empty()){
                    break;
                }
            }
            exit(0);

        } else {            // sorting is on    
            std::multimap<int, pair<string, aggrStruct>> aggrSortMap;
            //push agregates to multimap
            for(auto amIterator = aggrMap.begin(); amIterator != aggrMap.end(); amIterator++){
                if (params.sort.compare("bytes") == 0){
                    aggrSortMap.insert(make_pair(amIterator->second.aggrBytes, make_pair(amIterator->first,amIterator->second)));
                }else{
                    aggrSortMap.insert(make_pair(amIterator->second.aggrPackets, make_pair(amIterator->first,amIterator->second)));
                }
            }

            uint printCount = 0;    
            uint limit=0;
            if(!params.limit.empty())
                limit = stoul(params.limit, NULL,10);
            //print sorted multimap backwards
            for(auto sortIterator = --aggrSortMap.end(); sortIterator != --aggrSortMap.begin(); sortIterator--){
                printOneAgregate(sortIterator->second.first, sortIterator->second.second);
                printCount++;

                if(printCount >= limit && !params.limit.empty()){
                    break;
                }
            }
            exit(0);
        }
    }

    // sorting on
    if (params.sort.compare("bytes") == 0)
    {
        // push packets to multimap
        std::multimap<int, packetStruct> sortMap;
        for (auto pmIterator = pm.begin(); pmIterator != pm.end(); pmIterator++){ 
            sortMap.insert(make_pair(pmIterator->second.packetLength,pmIterator->second));
        }

        uint printCount = 0;
        uint limit=0;

        if(!params.limit.empty()){
            limit = stoul(params.limit, NULL, 10);
        }
        // print multimap backwards
        for(auto sortIterator = --sortMap.end(); sortIterator != --sortMap.begin(); sortIterator--){
            printPacket(sortIterator->second);
            printCount++;
            if(printCount >= limit && !params.limit.empty()){
                break;
            }
        }
        exit(0);
    }

    // no program parameter print
    uint printCount = 0;
    uint limit=0;

    if(!params.limit.empty()){
        limit = stoul(params.limit, NULL, 10);
    }

    for (auto pmIterator = pm.begin(); pmIterator != pm.end(); pmIterator++)
    { 
        printPacket(pmIterator->second);
        printCount++;
        if(printCount >= limit && !params.limit.empty()){
            break;
        }
    }
    return 0;
}
